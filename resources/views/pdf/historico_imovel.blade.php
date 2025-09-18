<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Consultas de Imóvel</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #222; }
        h1 { color: #1a237e; font-size: 22px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #bbb; padding: 6px 8px; text-align: left; }
        th { background: #e3e6f3; }
        .section { margin-bottom: 18px; }
        .small { font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <h1>Histórico de Consultas de Imóvel</h1>
    <div class="section">
        <strong>Cliente:</strong> {{ $customer->name }}<br>
        <strong>Email:</strong> {{ $customer->email }}<br>
        <span class="small">Gerado em: {{ date('d/m/Y H:i') }}</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Data/Hora</th>
                <th>Documento</th>
                <th>Nome Titular</th>
                <th>Município</th>
                <th>UF</th>
            </tr>
        </thead>
        <tbody>
        @foreach($historico as $item)
            <tr>
                <td>{{ $item->codigo_imovel }}</td>
                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                <td>{{ $item->resposta_api['titulares'][0]['cpfCnpj'] ?? '-' }}</td>
                <td>{{ $item->resposta_api['titulares'][0]['nomeTitular'] ?? '-' }}</td>
                <td>{{ $item->resposta_api['municipioSede'] ?? '-' }}</td>
                <td>{{ $item->resposta_api['ufSede'] ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html> 