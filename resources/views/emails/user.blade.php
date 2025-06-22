<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Bem-vindo à Easy-Split</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
  <table class="email-container">
    <tr>
      <td class="email-header">
        <h1>Bem-vindo(a)!</h1>
      </td>
    </tr>
    <tr>
      <td>
        <img src="{{ asset('images/Welcome-rafiki.svg') }}" alt="Bem-Vindo">
      </td>
    </tr>
    <tr>
      <td class="email-body">
        <p>Olá <strong>{{ Str::ucfirst($nome) }}</strong>,</p>
        <p>
          É um prazer ter você conosco na <strong>Easy-Split</strong>.
          Esperamos que você aproveite ao máximo os nossos serviços e conte sempre com a gente para o que precisar.
        </p>
        <p>Seja muito bem-vindo!</p>
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
