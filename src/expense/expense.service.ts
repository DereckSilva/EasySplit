import { HttpStatus, Injectable } from '@nestjs/common';
import { CreateExpenseDto } from './dto/create-expense.dto';
import { UpdateExpenseDto } from './dto/update-expense.dto';
import { Model } from 'mongoose';
import { Expense } from './interfaces/expense.interface';
import { InjectModel } from '@nestjs/mongoose';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { SlugService } from 'src/slug/slug.service';
import { UserService } from 'src/user/user.service';
import {
  ErrorDatePayment,
  ErrorEmptyIntermediary,
  ErrorFoundExpense,
  ErrorFoundUser,
  ErrorOldDatePayment,
  ErrorRemoveExpense,
} from 'src/errors/errors';

@Injectable()
export class ExpenseService {
  constructor(
    @InjectModel('Expense') private readonly expenseModel: Model<Expense>,
    private readonly eventEmitter: EventEmitter2,
    private readonly slugService: SlugService,
    private readonly userService: UserService,
  ) {}

  async create(createExpenseDto: CreateExpenseDto) {
    const user = await this.userService.findOne(createExpenseDto.payeeId)[0];
    if (user == null) {
      throw new ErrorFoundUser();
    }

    createExpenseDto.maturityPayment = this.defineMaturityDate(
      createExpenseDto.datePayment.toString(),
      createExpenseDto.parcels,
    );

    createExpenseDto = {
      ...createExpenseDto,
      slug: this.slugService.createSlug(
        `${createExpenseDto.description} ${createExpenseDto.datePayment}`,
      ),
    };

    this.eventEmitter.emit('expense.created', createExpenseDto);
    const expense = (await new this.expenseModel(
      createExpenseDto,
    ).save()) as Expense;
    return [expense];
  }

  async findAll() {
    const expense = await this.expenseModel.find();
    if (expense.length === 0) {
      return [
        {
          message: 'Nenhuma conta foi cadastrada',
          statusCode: HttpStatus.NO_CONTENT,
          data: {},
        },
      ];
    }
    return expense;
  }

  async findOne(id: string) {
    const expense = (await this.expenseModel
      .findOne({ _id: id })
      .exec()) as Expense;
    return [expense];
  }

  async update(id: string, updateExpenseDto: UpdateExpenseDto) {
    const user = await this.userService.findOne(updateExpenseDto.payeeId);
    if (user.length === 0) {
      throw new ErrorFoundUser();
    }
    this.eventEmitter.emit('expense.updated', { id, updateExpenseDto });
    const expenseOld = await this.findOne(id)[0];
    if (expenseOld == null) {
      throw new ErrorFoundExpense();
    }
    const expense = await this.expenseModel.updateOne(
      { id: id },
      {
        $set: {
          ...updateExpenseDto,
        },
      },
    );
    return [expense];
  }

  async remove(id: string) {
    try {
      const expense = await this.findOne(id)[0];
      if (expense == null) {
        throw new ErrorFoundExpense();
      }
      await this.expenseModel.deleteOne({ _id: id });
      return true;
    } catch (error) {
      console.log(error);
      throw new ErrorRemoveExpense();
    }
  }

  findExpense(id: string) {
    const expense = this.findOne(id)[0];
    if (expense === null) {
      throw new ErrorFoundExpense();
    }
  }

  verifierIntermediary(createExpenseDto: CreateExpenseDto | UpdateExpenseDto) {
    const intermediaryIds = createExpenseDto.intermediaryIds.filter(
      (value) => value !== null || value !== undefined,
    );

    if (createExpenseDto.intermediary && intermediaryIds.length === 0) {
      throw new ErrorEmptyIntermediary();
    }
  }

  defineMaturityDate(datePayment: string, parcels: number) {
    const currentDateMilliseconds = Date.now();
    const currentDate = new Date(currentDateMilliseconds);

    if (new Date(datePayment).toString() === 'Invalid Date') {
      throw new ErrorDatePayment();
    }

    if (currentDateMilliseconds > Date.parse(datePayment)) {
      throw new ErrorOldDatePayment();
    }

    const maturityPayment = new Date(datePayment);
    maturityPayment.setDate(maturityPayment.getDate() + 1);
    maturityPayment.setMonth(maturityPayment.getMonth() + parcels);
    maturityPayment.setHours(currentDate.getHours() - 3);
    maturityPayment.setMinutes(currentDate.getMinutes());
    maturityPayment.setSeconds(currentDate.getSeconds());
    return maturityPayment;
  }
}
