<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Senha alterada com sucesso</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
  <table class="email-container">
    <tr>
      <td class="email-header">
        <h1>Senha Alterada</h1>
      </td>
    </tr>
    <tr>
      <td>
        <img src="{{ asset('images/Forgot password-pana.svg') }}" alt="Esqueceu Senha">
      </td>
    </tr>
    <tr>
      <td class="email-body">
        <p>Olá <strong>{{ Str::ucfirst($nome) }}</strong>,</p>
        <p>
          Informamos que a sua senha foi alterada com sucesso em nossa plataforma.
          Se você reconhece essa alteração, não precisa fazer mais nada.
        </p>
        <p>
          Caso não tenha realizado essa mudança, entre em contato conosco imediatamente.
        </p>
      </td>
    </tr>
    <tr>
      <td class="email-footer">
        <p>
          Easy-Split · CNPJ 00.000.000/0001-00<br/>
          Rua Exemplo, 123 - Bairro - Cidade/UF<br/>
          suporte@empresa.com.br · (11) 1234-5678
        </p>
      </td>
    </tr>
  </table>
</body>
</html>
