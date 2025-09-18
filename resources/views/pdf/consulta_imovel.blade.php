<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Imóvel</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #222; }
        h1 { color: #1a237e; font-size: 22px; margin-bottom: 10px; }
        .section { margin-bottom: 18px; }
        .small { font-size: 11px; color: #666; }
        .dados { margin-bottom: 10px; }
        .dados span { display: inline-block; min-width: 120px; font-weight: bold; }
        ul { margin: 0 0 10px 18px; }
    </style>
</head>
<body>
    <h1>Consulta de Imóvel</h1>
    <div class="section">
        <strong>Cliente:</strong> {{ $customer->name }}<br>
        <strong>Email:</strong> {{ $customer->email }}<br>
        <span class="small">Gerado em: {{ date('d/m/Y H:i') }}</span>
    </div>
    <div class="section">
        <div class="dados"><span>Código INCRA:</span> {{ $consulta->resposta_api['codigoImovelIncra'] ?? '-' }}</div>
        <div class="dados"><span>Denominação:</span> {{ $consulta->resposta_api['denominacao'] ?? '-' }}</div>
        <div class="dados"><span>Área Total:</span> {{ $consulta->resposta_api['areaTotal'] ?? '-' }} ha</div>
        <div class="dados"><span>Classificação:</span> {{ $consulta->resposta_api['classificacaoFundiaria'] ?? '-' }}</div>
        <div class="dados"><span>Município:</span> {{ $consulta->resposta_api['municipioSede'] ?? '-' }}</div>
        <div class="dados"><span>UF:</span> {{ $consulta->resposta_api['ufSede'] ?? '-' }}</div>
        <div class="dados"><span>Data Última Declaração:</span> {{ $consulta->resposta_api['dataProcessamentoUltimaDeclaracao'] ?? '-' }}</div>
    </div>
    @if(!empty($consulta->resposta_api['titulares']))
        <div class="section">
            <strong>Titulares:</strong>
            <ul>
                @foreach($consulta->resposta_api['titulares'] as $tit)
                    <li>{{ $tit['nomeTitular'] ?? '-' }} ({{ $tit['cpfCnpj'] ?? '-' }}) - {{ $tit['condicaoTitularidade'] ?? '-' }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(!empty($consulta->resposta_api['areasRegistradas']))
        <div class="section">
            <strong>Áreas Registradas:</strong>
            <ul>
                @foreach($consulta->resposta_api['areasRegistradas'] as $area)
                    <li>{{ $area['municipioCartorio'] ?? '-' }}/{{ $area['ufCartorio'] ?? '-' }} - Matrícula: {{ $area['matriculaOuTranscricao'] ?? '-' }} - Área: {{ $area['area'] ?? '-' }} ha</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(!empty($consulta->resposta_api['dadosUltimoCcir']))
        <div class="section">
            <strong>Dados Último CCIR:</strong>
            <div class="dados"><span>Nº CCIR:</span> {{ $consulta->resposta_api['dadosUltimoCcir']['numeroCcir'] ?? '-' }}</div>
            <div class="dados"><span>Situação:</span> {{ $consulta->resposta_api['dadosUltimoCcir']['situacaoCcir'] ?? '-' }}</div>
            <div class="dados"><span>Valor Total:</span> R$ {{ $consulta->resposta_api['dadosUltimoCcir']['valorTotal'] ?? '-' }}</div>
            <div class="dados"><span>Data Geração:</span> {{ $consulta->resposta_api['dadosUltimoCcir']['dataGeracaoCcir'] ?? '-' }}</div>
        </div>
    @endif
</body>
</html> 