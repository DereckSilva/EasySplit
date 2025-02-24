import { Document } from 'mongoose';

export interface Expense extends Document {
  readonly description: string;
  readonly price: number;
  readonly parcels: number;
  readonly payeeId: string;
  readonly intermediaryId: string;
  readonly intermediary: boolean;
  readonly datePayment: Date;
  readonly slug: string;
}
