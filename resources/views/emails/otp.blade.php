<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de acesso</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 480px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .header { background: #111827; padding: 24px 32px; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; letter-spacing: .5px; }
        .body { padding: 32px; }
        .body p { color: #374151; line-height: 1.6; margin: 0 0 16px; }
        .otp-box { background: #f9fafb; border: 2px dashed #d1d5db; border-radius: 8px; text-align: center; padding: 24px; margin: 24px 0; }
        .otp-code { font-size: 40px; font-weight: 700; letter-spacing: 12px; color: #111827; }
        .expiry { color: #6b7280; font-size: 13px; margin-top: 8px; }
        .footer { padding: 16px 32px; background: #f9fafb; border-top: 1px solid #e5e7eb; }
        .footer p { color: #9ca3af; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Chat Dolar</h1>
        </div>
        <div class="body">
            <p>Olá, <strong>{{ $userName }}</strong>.</p>
            <p>O seu código de verificação de acesso é:</p>
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <div class="expiry">Válido por <strong>5 minutos</strong></div>
            </div>
            <p>Se não solicitou este código, ignore este email. A sua conta permanece segura.</p>
        </div>
        <div class="footer">
            <p>Este é um email automático — não responda.</p>
        </div>
    </div>
</body>
</html>