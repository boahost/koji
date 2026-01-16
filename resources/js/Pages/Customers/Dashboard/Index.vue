<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import CustomerDashboardLayout from '@/Layouts/CustomerDashboardLayout.vue';
import BottomNavLayout from '@/Layouts/BottomNavLayout.vue';
import { ref, watch, onMounted, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    customer: Object,
    ordersCount: Number,
    cartItemsCount: Number,
    walletValue: Number,
});

const form = useForm({
    valor: ''
});

const valorFormatado = ref('');

// Função para exibir valores (usada no template)
function formatarBRL(valor) {
    if (valor === null || valor === undefined) return '0,00';
    
    // Converte para número
    const numero = parseFloat(valor);
    if (isNaN(numero)) return '0,00';
    
    // Formata como moeda brasileira
    return numero.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Função específica para formatação de input
function formatarInputBRL(valor) {
    if (!valor) return '';
    
    // Remove tudo que não é número
    let num = valor.replace(/\D/g, '');
    if (num.length === 0) return '';
    
    // Remove zeros à esquerda
    num = num.replace(/^0+(?!$)/, '');
    if (num.length === 1) num = '0' + num; // Para garantir pelo menos 2 dígitos
    
    let inteiro = num.slice(0, -2);
    let decimal = num.slice(-2);
    if (inteiro === '') inteiro = '0';
    
    // Adiciona pontos para milhares
    let inteiroFormatado = inteiro.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    
    return inteiroFormatado + ',' + decimal;
}

function parseBRLtoNumber(valor) {
    if (!valor) return 0;
    return parseFloat(valor.replace(/\./g, '').replace(',', '.'));
}

watch(valorFormatado, (novoValor) => {
    valorFormatado.value = formatarInputBRL(novoValor);
    form.valor = parseBRLtoNumber(valorFormatado.value);
});

function onInput(e) {
    valorFormatado.value = formatarInputBRL(e.target.value);
    form.valor = parseBRLtoNumber(valorFormatado.value);
}

function submit() {
    form.valor = parseBRLtoNumber(valorFormatado.value);
    form.post(route('customer.wallet.add'), {
        onSuccess: () => {
            form.reset('valor');
            valorFormatado.value = '';
        }
    });
}

const imovelCodigo = ref('');
const imovelResultado = ref(null);
const imovelErro = ref('');
const buscandoImovel = ref(false);
const historicoImovel = ref([]);
const historicoPorNI = ref([]);
const historicoPorCodigo = ref([]);
const carregandoHistoricoCodigo = ref(true);
const carregandoHistoricoNI = ref(true);
const mostrarDetalhe = ref(null);
let filtroHistorico = ref('');
const itensExibidos = ref(1); // Mostra apenas 1 item inicialmente
const itensPorPagina = 1; // Quantos itens carregar por vez

// Novas variáveis para busca por NI
const abaAtiva = ref('codigo'); // 'codigo' ou 'ni'
const niCpf = ref('');
const codigosEncontrados = ref([]);
const buscandoPorNI = ref(false);
const erroNI = ref('');

function onSearchHistorico(valor) {
    filtroHistorico.value = valor.toLowerCase();
    // Reseta o contador quando o filtro muda
    itensExibidos.value = 1;
    // Recarrega os históricos quando o filtro muda
    if (abaAtiva.value === 'codigo') {
        carregarHistoricoPorCodigo();
    } else {
        carregarHistoricoPorNI();
    }
}

async function carregarHistoricoImovel() {
    try {
        const response = await axios.get(route('customer.historico-imovel'));
        historicoImovel.value = response.data.historico;
        console.log('Histórico carregado:', historicoImovel.value);
    } catch (e) {
        console.error('Erro ao carregar histórico:', e);
        historicoImovel.value = [];
    }
}

async function carregarHistoricoPorNI() {
    try {
        const response = await axios.get(route('customer.historico-por-ni'));
        historicoPorNI.value = response.data.historico;
        console.log('Histórico por NI carregado:', historicoPorNI.value);
    } catch (e) {
        console.error('Erro ao carregar histórico por NI:', e);
        historicoPorNI.value = [];
    } finally {
        carregandoHistoricoNI.value = false;
    }
}

async function carregarHistoricoPorCodigo() {
    try {
        const response = await axios.get(route('customer.historico-por-codigo'));
        historicoPorCodigo.value = response.data.historico;
        console.log('Histórico por código carregado:', historicoPorCodigo.value);
    } catch (e) {
        console.error('Erro ao carregar histórico por código:', e);
        historicoPorCodigo.value = [];
    } finally {
        carregandoHistoricoCodigo.value = false;
    }
}

onMounted(() => {
    carregarHistoricoPorCodigo();
    carregarHistoricoPorNI();
});

async function buscarImovel() {
    imovelErro.value = '';
    imovelResultado.value = null;
    
    // Validação rigorosa de saldo
    if (props.walletValue < 20) {
        imovelErro.value = 'Você precisa de pelo menos R$ 20,00 em saldo para buscar um imóvel. Adicione saldo para continuar.';
        return;
    }
    
    // Validação de entrada
    if (!imovelCodigo.value || imovelCodigo.value.trim() === '') {
        imovelErro.value = 'Digite o código do imóvel.';
        return;
    }
    
    // Sanitização do código (apenas números)
    const codigoLimpo = imovelCodigo.value.replace(/\D/g, '');
    
    // Prevenir múltiplas requisições
    if (buscandoImovel.value) {
        return;
    }
    
    // Confirmação com SweetAlert2
    const result = await Swal.fire({
        title: 'Confirmar Consulta',
        text: `Deseja consultar o imóvel com código ${codigoLimpo}? Esta consulta custará R$ 20,00 do seu saldo.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, consultar!',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    });
    
    if (!result.isConfirmed) {
        return;
    }
    
    buscandoImovel.value = true;
    
    try {
        const response = await axios.post(route('customer.buscar-imovel'), { 
            codigo: codigoLimpo 
        });
        
        // Validação da resposta
        if (response.data && response.data.data) {
            imovelResultado.value = response.data.data;
            await carregarHistoricoPorCodigo();
            // Atualiza saldo na tela após consulta bem-sucedida
            setTimeout(() => {
                window.location.reload();
            }, 1000); // Aguarda 1 segundo para mostrar o resultado
        } else {
            imovelErro.value = 'Resposta inválida do servidor.';
        }
    } catch (e) {
        console.error('Erro na busca:', e);
        if (e.response?.status === 402) {
            imovelErro.value = 'Saldo insuficiente. Você precisa de pelo menos R$ 20,00.';
        } else if (e.response?.status === 404) {
            imovelErro.value = 'Imóvel não encontrado. Verifique o código e tente novamente.';
        } else if (e.response?.status === 429) {
            imovelErro.value = 'Muitas tentativas. Aguarde um momento e tente novamente.';
        } else {
            imovelErro.value = e.response?.data?.error || 'Erro ao buscar imóvel. Tente novamente.';
        }
    } finally {
        buscandoImovel.value = false;
    }
}

// Sugestões dinâmicas para busca
const sugestoesBusca = computed(() => {
    if (!filtroHistorico.value) return [];
    return historicoImovel.value.filter(h =>
        JSON.stringify(h.resposta_api).toLowerCase().includes(filtroHistorico.value)
    ).slice(0, 8); // Limita a 8 sugestões
});

// Histórico filtrado e limitado por código
const historicoFiltradoPorCodigo = computed(() => {
    const filtrado = historicoPorCodigo.value.filter(h => 
        filtroHistorico.value === '' || JSON.stringify(h.resposta_api).toLowerCase().includes(filtroHistorico.value)
    );
    return filtrado.slice(0, itensExibidos.value);
});

// Histórico filtrado e limitado por NI
const historicoFiltradoPorNI = computed(() => {
    const filtrado = historicoPorNI.value.filter(h => 
        filtroHistorico.value === '' || JSON.stringify(h.resposta_api).toLowerCase().includes(filtroHistorico.value)
    );
    return filtrado.slice(0, itensExibidos.value);
});

// Verifica se há mais itens para mostrar (código)
const temMaisItensCodigo = computed(() => {
    const filtrado = historicoPorCodigo.value.filter(h => 
        filtroHistorico.value === '' || JSON.stringify(h.resposta_api).toLowerCase().includes(filtroHistorico.value)
    );
    return itensExibidos.value < filtrado.length;
});

// Verifica se há mais itens para mostrar (NI)
const temMaisItensNI = computed(() => {
    const filtrado = historicoPorNI.value.filter(h => 
        filtroHistorico.value === '' || JSON.stringify(h.resposta_api).toLowerCase().includes(filtroHistorico.value)
    );
    return itensExibidos.value < filtrado.length;
});

// Função para carregar mais itens
function carregarMaisItens() {
    itensExibidos.value += itensPorPagina;
}

// Função para buscar imóveis por NI (CPF/CNPJ)
async function buscarPorNI() {
    erroNI.value = '';
    codigosEncontrados.value = [];
    
    // Validação rigorosa de saldo
    if (props.walletValue < 20) {
        erroNI.value = 'Você precisa de pelo menos R$ 20,00 em saldo para buscar imóveis. Adicione saldo para continuar.';
        return;
    }
    
    // Validação de entrada
    if (!niCpf.value || niCpf.value.trim() === '') {
        erroNI.value = 'Digite o CPF/CNPJ.';
        return;
    }
    
    // Sanitização e validação do CPF/CNPJ (apenas números)
    const niLimpo = niCpf.value.replace(/\D/g, '');
    
    // Validação rigorosa do formato
    if (niLimpo.length !== 11 && niLimpo.length !== 14) {
        erroNI.value = 'CPF deve ter 11 dígitos ou CNPJ deve ter 14 dígitos.';
        return;
    }
    
    // Validação adicional para CPF (11 dígitos)
    if (niLimpo.length === 11) {
        // Verificar se não são todos os mesmos números
        if (/^(\d)\1{10}$/.test(niLimpo)) {
            erroNI.value = 'CPF inválido.';
            return;
        }
    }
    
    // Validação adicional para CNPJ (14 dígitos)
    if (niLimpo.length === 14) {
        // Verificar se não são todos os mesmos números
        if (/^(\d)\1{13}$/.test(niLimpo)) {
            erroNI.value = 'CNPJ inválido.';
            return;
        }
    }
    
    // Prevenir múltiplas requisições
    if (buscandoPorNI.value) {
        return;
    }
    
    // Confirmação com SweetAlert2
    const result = await Swal.fire({
        title: 'Confirmar Consulta',
        text: `Deseja consultar imóveis pelo CPF/CNPJ ${niLimpo}? Esta consulta custará R$ 20,00 do seu saldo.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, consultar!',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    });
    
    if (!result.isConfirmed) {
        return;
    }
    
    buscandoPorNI.value = true;
    
    try {
        console.log('Fazendo busca por NI:', niLimpo);
        const response = await axios.post(route('customer.buscar-por-ni'), { 
            ni: niLimpo 
        });
        console.log('Resposta da busca por NI:', response.data);
        
        // Validação da resposta
        if (response.data && response.data.data && Array.isArray(response.data.data)) {
            if (response.data.data.length > 0) {
                codigosEncontrados.value = response.data.data;
                // Atualiza saldo na tela após consulta bem-sucedida
                setTimeout(() => {
                    window.location.reload();
                }, 1000); // Aguarda 1 segundo para mostrar o resultado
            } else {
                codigosEncontrados.value = [];
                erroNI.value = 'Nenhum imóvel encontrado para este CPF/CNPJ.';
            }
        } else {
            erroNI.value = 'Resposta inválida do servidor.';
        }
        
        // Sempre atualiza o histórico por NI
        await carregarHistoricoPorNI();
    } catch (error) {
        console.error('Erro na busca por NI:', error);
        
        // Tratamento específico de erros
        if (error.response?.status === 402) {
            erroNI.value = 'Saldo insuficiente. Você precisa de pelo menos R$ 20,00.';
        } else if (error.response?.status === 404) {
            erroNI.value = 'CPF/CNPJ não encontrado. Verifique os dados e tente novamente.';
        } else if (error.response?.status === 429) {
            erroNI.value = 'Muitas tentativas. Aguarde um momento e tente novamente.';
        } else if (error.response?.status === 422) {
            erroNI.value = 'CPF/CNPJ inválido. Verifique os dados e tente novamente.';
        } else {
            erroNI.value = error.response?.data?.error || 'Erro ao buscar imóveis. Tente novamente.';
        }
        
        // Atualiza o histórico mesmo em caso de erro
        await carregarHistoricoPorNI();
    } finally {
        buscandoPorNI.value = false;
    }
}

// Função para consultar um código específico encontrado
async function consultarCodigoEncontrado(codigo) {
    imovelCodigo.value = codigo;
    try {
        await buscarImovel();
        // Atualiza o histórico por código após a consulta
        await carregarHistoricoPorCodigo();
    } catch (error) {
        console.error('Erro ao consultar código:', error);
    }
}
</script>

<template>
    <BottomNavLayout>
        <Head title="Dashboard" />

        <CustomerDashboardLayout :customer="customer" @search-historico="onSearchHistorico">
            <div class="animate-fade-in-up">
                <!-- Sugestões dinâmicas de busca -->
                <div v-if="filtroHistorico && sugestoesBusca.length" style="position:fixed;top:115px;left:0;right:0;z-index:60;" class="flex justify-center pointer-events-none">
                    <div class="bg-white border border-gray-200 rounded-lg shadow-lg w-full max-w-2xl mx-auto pointer-events-auto">
                        <ul>
                            <li v-for="item in sugestoesBusca" :key="item.id" class="px-4 py-2 hover:bg-blue-50 cursor-pointer border-b last:border-b-0" @click="mostrarDetalhe = item">
                                <div class="font-semibold text-blue-900">{{ item.resposta_api.titulares?.[0]?.cpfCnpj || 'Sem documento' }}</div>
                                <div class="text-xs text-gray-600">{{ item.resposta_api.titulares?.[0]?.nomeTitular || 'Sem nome' }}</div>
                                <div class="text-xs text-gray-500">Matrícula: {{ item.resposta_api.areasRegistradas?.[0]?.matriculaOuTranscricao || 'N/A' }}</div>
                                <div class="text-xs text-gray-400">Código: {{ item.codigo_imovel }}</div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Welcome Section -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-black to-gray-900 bg-clip-text text-transparent">
                        Bem-vindo(a) ao seu Dashboard
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Gerencie seus pedidos e informações pessoais
                    </p>
                </div>

                <!-- Saldo da Carteira -->
                <div class="mt-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-3xl p-8 shadow-lg border border-green-200">
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                        <!-- Informações do Saldo -->
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div class="bg-gradient-to-br from-green-400 to-emerald-600 text-white rounded-2xl p-4 shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                </div>
                                <!-- Indicador de status -->
                                <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full border-4 border-white shadow-sm flex items-center justify-center">
                                    <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                </div>
                            </div>
                            
                            <div class="text-center lg:text-left">
                                <div class="text-sm font-medium text-green-700 uppercase tracking-wide">Saldo Disponível</div>
                                <div class="text-4xl lg:text-5xl font-bold bg-gradient-to-r from-green-600 to-emerald-700 bg-clip-text text-transparent mt-1">
                                    R$ {{ formatarBRL(walletValue) }}
                                </div>
                                <div class="text-sm text-green-600 mt-2 flex items-center justify-center lg:justify-start gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Saldo ativo para consultas
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formulário de Adição -->
                        <div class="w-full lg:w-auto">
                            <form @submit.prevent="submit" class="bg-white rounded-2xl p-6 shadow-lg border border-green-100">
                                <div class="text-center mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Adicionar Saldo</h3>
                                    <p class="text-sm text-gray-600 mt-1">Cada consulta custa R$ 20,00</p>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-gray-500 text-lg font-medium">R$</span>
                                        </div>
                                        <input
                                            type="text"
                                            :value="valorFormatado"
                                            @input="onInput"
                                            placeholder="0,00"
                                            class="block w-full pl-12 pr-4 py-4 text-lg font-semibold border-2 border-green-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                            :disabled="form.processing"
                                            required
                                        />
                                    </div>
                                    
                                    <button
                                        type="submit"
                                        class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-100 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                                        :disabled="form.processing"
                                    >
                                        <span class="inline-flex items-center gap-2 text-lg">
                                            <svg v-if="!form.processing" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            <svg v-else class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ form.processing ? 'Processando...' : 'Adicionar Saldo' }}
                                        </span>
                                    </button>
                                </div>
                                
                                <!-- Informações adicionais -->
                                <div class="mt-4 text-center">
                                    <div class="text-xs text-gray-500 space-y-1">
                                        <div class="flex items-center justify-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Pagamento seguro
                                        </div>
                                        <div class="flex items-center justify-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Saldo disponível imediatamente
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Navbar de Busca -->
                <div class="mt-6 flex justify-center">
                    <div class="bg-white rounded-2xl p-1 shadow-sm border border-gray-200 flex">
                        <button 
                            @click="abaAtiva = 'codigo'" 
                            :class="[
                                'px-6 py-3 rounded-xl text-sm font-medium transition-all duration-200',
                                abaAtiva === 'codigo' 
                                    ? 'bg-blue-600 text-white shadow-sm' 
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                            ]"
                        >
                            Buscar por Código
                        </button>
                        <button 
                            @click="abaAtiva = 'ni'" 
                            :class="[
                                'px-6 py-3 rounded-xl text-sm font-medium transition-all duration-200',
                                abaAtiva === 'ni' 
                                    ? 'bg-blue-600 text-white shadow-sm' 
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                            ]"
                        >
                            Buscar por NI
                        </button>
                    </div>
                </div>

                <!-- Busca de Imóvel por Código -->
                <div v-if="abaAtiva === 'codigo'" class="mt-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-blue-200 flex flex-col gap-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="bg-blue-100 text-blue-700 rounded-full p-3 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                            </div>
                            <label class="block text-lg font-bold text-blue-900">Buscar Imóvel por Código </label>
                        </div>
                        <div class="text-xs text-gray-500 mb-2">Consulte dados de um imóvel rural pelo código. Cada consulta custa R$ 20,00 do seu saldo.</div>
                        <div class="flex gap-2">
                            <input
                                v-model="imovelCodigo"
                                type="text"
                                placeholder="Digite o código do imóvel"
                                class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 w-full transition-all duration-200"
                                :class="[
                                    props.walletValue >= 20
                                        ? 'border-blue-300 focus:ring-blue-500 focus:border-blue-500'
                                        : 'border-gray-300 bg-gray-100 cursor-not-allowed'
                                ]"
                                :disabled="buscandoImovel || (props.walletValue < 20)"
                            />
                            <button
                                @click="buscarImovel"
                                class="font-semibold px-6 py-2 rounded-lg transition-all duration-200 shadow"
                                :class="[
                                    props.walletValue >= 20 && !buscandoImovel
                                        ? 'bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 text-white hover:shadow-lg'
                                        : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                ]"
                                :disabled="buscandoImovel || (props.walletValue < 20)"
                            >
                                <span class="inline-flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l-4-4m0 0l4-4m-4 4h18" /></svg>
                                    Buscar
                                </span>
                            </button>
                        </div>
                        <div v-if="imovelErro" class="text-red-600 mt-2 text-sm">{{ imovelErro }}</div>
                        <!-- Resultado bonito -->
                        <div v-if="buscandoImovel" class="mt-6 bg-blue-50 rounded-lg p-4 border border-blue-200 animate-pulse">
                            <div class="h-4 w-40 bg-blue-100 rounded mb-4"></div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="h-4 bg-blue-100 rounded"></div>
                                <div class="h-4 bg-blue-100 rounded"></div>
                                <div class="h-4 bg-blue-100 rounded"></div>
                                <div class="h-4 bg-blue-100 rounded"></div>
                            </div>
                        </div>
                        <div v-else-if="imovelResultado" class="mt-6 bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Dados do Imóvel</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div><span class="font-medium">Código INCRA:</span> {{ imovelResultado.codigoImovelIncra }}</div>
                                <div><span class="font-medium">Denominação:</span> {{ imovelResultado.denominacao }}</div>
                                <div><span class="font-medium">Área Total:</span> {{ imovelResultado.areaTotal }} ha</div>
                                <div><span class="font-medium">Classificação:</span> {{ imovelResultado.classificacaoFundiaria }}</div>
                                <div><span class="font-medium">Município:</span> {{ imovelResultado.municipioSede }}</div>
                                <div><span class="font-medium">UF:</span> {{ imovelResultado.ufSede }}</div>
                                <div><span class="font-medium">Data Última Declaração:</span> {{ imovelResultado.dataProcessamentoUltimaDeclaracao }}</div>
                            </div>
                            <div v-if="imovelResultado.titulares && imovelResultado.titulares.length" class="mt-4">
                                <h4 class="font-semibold mb-1">Titulares:</h4>
                                <ul class="list-disc ml-6">
                                    <li v-for="tit in imovelResultado.titulares" :key="tit.cpfCnpj">
                                        {{ tit.nomeTitular }} ({{ tit.cpfCnpj }}) - {{ tit.condicaoTitularidade }}
                                    </li>
                                </ul>
                            </div>
                            <div v-if="imovelResultado.areasRegistradas && imovelResultado.areasRegistradas.length" class="mt-4">
                                <h4 class="font-semibold mb-1">Áreas Registradas:</h4>
                                <ul class="list-disc ml-6">
                                    <li v-for="area in imovelResultado.areasRegistradas" :key="area.matriculaOuTranscricao">
                                        {{ area.municipioCartorio }}/{{ area.ufCartorio }} - Matrícula: {{ area.matriculaOuTranscricao }} - Área: {{ area.area }} ha
                                    </li>
                                </ul>
                            </div>
                            <div v-if="imovelResultado.dadosUltimoCcir" class="mt-4">
                                <h4 class="font-semibold mb-1">Dados Último CCIR:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <div><span class="font-medium">Nº CCIR:</span> {{ imovelResultado.dadosUltimoCcir.numeroCcir }}</div>
                                    <div><span class="font-medium">Situação:</span> {{ imovelResultado.dadosUltimoCcir.situacaoCcir }}</div>
                                    <div><span class="font-medium">Valor Total:</span> R$ {{ imovelResultado.dadosUltimoCcir.valorTotal }}</div>
                                    <div><span class="font-medium">Data Geração:</span> {{ imovelResultado.dadosUltimoCcir.dataGeracaoCcir }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Busca de Imóvel por NI -->
                <div v-if="abaAtiva === 'ni'" class="mt-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-green-200 flex flex-col gap-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="bg-green-100 text-green-700 rounded-full p-3 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <label class="block text-lg font-bold text-green-900">Buscar Imóvel por NI (CPF/CNPJ)</label>
                        </div>
                        <div class="text-xs text-gray-500 mb-2">Consulte códigos de imóveis rurais pelo CPF/CNPJ do proprietário. Cada consulta custa R$ 20,00 do seu saldo.</div>
                        <div class="flex gap-2">
                            <input
                                v-model="niCpf"
                                type="text"
                                placeholder="Digite o CPF ou CNPJ"
                                class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 w-full transition-all duration-200"
                                :class="[
                                    props.walletValue >= 20
                                        ? 'border-green-300 focus:ring-green-500 focus:border-green-500'
                                        : 'border-gray-300 bg-gray-100 cursor-not-allowed'
                                ]"
                                :disabled="buscandoPorNI || (props.walletValue < 20)"
                            />
                            <button
                                @click="buscarPorNI"
                                class="font-semibold px-6 py-2 rounded-lg transition-all duration-200 shadow"
                                :class="[
                                    props.walletValue >= 20 && !buscandoPorNI
                                        ? 'bg-gradient-to-r from-green-500 to-green-700 hover:from-green-600 hover:to-green-800 text-white hover:shadow-lg'
                                        : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                ]"
                                :disabled="buscandoPorNI || (props.walletValue < 20)"
                            >
                                <span class="inline-flex items-center gap-1">
                                    <svg v-if="!buscandoPorNI" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    <svg v-else class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Buscar
                                </span>
                            </button>
                        </div>
                        <div v-if="erroNI" class="text-red-600 mt-2 text-sm">{{ erroNI }}</div>
                        
                        <!-- Resultado dos códigos encontrados -->
                        <div v-if="buscandoPorNI" class="mt-6 bg-green-50 rounded-lg p-4 border border-green-200 animate-pulse">
                            <div class="h-4 w-56 bg-green-100 rounded mb-3"></div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                <div v-for="n in 3" :key="n" class="bg-white rounded-lg p-3 border border-green-100">
                                    <div class="h-4 w-24 bg-green-100 rounded mb-2"></div>
                                    <div class="h-3 w-20 bg-green-100 rounded"></div>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="codigosEncontrados.length > 0" class="mt-6 bg-green-50 rounded-lg p-4 border border-green-200">
                            <h3 class="text-lg font-bold text-green-900 mb-3">Códigos de Imóveis Encontrados</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                <div 
                                    v-for="codigo in codigosEncontrados" 
                                    :key="codigo"
                                    class="bg-white rounded-lg p-3 border border-green-200 hover:border-green-300 transition-colors cursor-pointer"
                                    @click="consultarCodigoEncontrado(codigo)"
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="font-mono text-sm text-green-800">{{ codigo }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </div>
                                    <p class="text-xs text-green-600 mt-1">Clique para consultar</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Histórico de Consultas por NI -->
                <div v-if="abaAtiva === 'ni'" class="mt-6">
                    <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow-sm border border-green-200">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-bold text-green-900">Histórico de Consultas por NI</h3>
                        </div>
                        <div v-if="carregandoHistoricoNI" class="flex flex-col gap-4">
                            <div v-for="n in 2" :key="n" class="rounded-xl border border-green-100 bg-green-50 p-4 shadow-sm animate-pulse">
                                <div class="flex justify-between mb-3">
                                    <div class="h-4 w-24 bg-green-100 rounded-full"></div>
                                    <div class="h-3 w-16 bg-green-100 rounded-full"></div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div class="h-4 bg-green-100 rounded"></div>
                                    <div class="h-4 bg-green-100 rounded"></div>
                                    <div class="h-4 bg-green-100 rounded"></div>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="historicoPorNI.length === 0" class="text-gray-500">Nenhuma consulta por NI realizada ainda.</div>
                        <div v-else class="flex flex-col gap-4">
                            <div v-for="item in historicoFiltradoPorNI" :key="item.id" class="rounded-xl border border-green-100 bg-green-50 p-4 flex flex-col gap-2 shadow-sm hover:shadow-md transition-all">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Busca por NI
                                    </span>
                                    <span class="text-xs text-gray-500">{{ new Date(item.created_at).toLocaleDateString('pt-BR') }}</span>
                                </div>
                                
                                <div class="flex-1 flex flex-col md:flex-row md:items-center gap-2">
                                    <div class="flex flex-col min-w-[120px]">
                                        <span class="text-xs text-gray-500">NI Consultado</span>
                                        <span class="font-bold text-green-900">{{ item.ni_consultado || '-' }}</span>
                                    </div>
                                    <div class="flex flex-col min-w-[160px]">
                                        <span class="text-xs text-gray-500">Códigos Encontrados</span>
                                        <span class="font-semibold text-gray-900">{{ item.resposta_api.length || 0 }} imóveis</span>
                                    </div>
                                    <div class="flex flex-col min-w-[200px]">
                                        <span class="text-xs text-gray-500">Códigos</span>
                                        <span class="text-gray-800 text-sm">{{ item.resposta_api.slice(0, 3).join(', ') }}{{ item.resposta_api.length > 3 ? '...' : '' }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-3 mt-3 sm:justify-end">
                                    <button @click="mostrarDetalhe = item" class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-full transition-all duration-200" title="Ver Códigos">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botão Ver Mais para NI -->
                        <div v-if="temMaisItensNI" class="mt-4 flex justify-center">
                            <button 
                                @click="carregarMaisItens" 
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-green-700 bg-green-100 border border-green-300 rounded-lg hover:bg-green-200 hover:text-green-800 transition-all duration-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                Ver Mais
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Histórico de Consultas por Código -->
                <div v-if="abaAtiva === 'codigo'" class="mt-6">
                    <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow-sm border border-yellow-200">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-bold text-yellow-900">Histórico de Consultas por Código</h3>
                        </div>
                        <div v-if="carregandoHistoricoCodigo" class="flex flex-col gap-4">
                            <div v-for="n in 2" :key="n" class="rounded-xl border border-yellow-100 bg-amber-50 p-4 shadow-sm animate-pulse">
                                <div class="flex justify-between mb-3">
                                    <div class="h-4 w-24 bg-amber-100 rounded-full"></div>
                                    <div class="h-3 w-16 bg-amber-100 rounded-full"></div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                    <div class="h-4 bg-amber-100 rounded"></div>
                                    <div class="h-4 bg-amber-100 rounded"></div>
                                    <div class="h-4 bg-amber-100 rounded"></div>
                                    <div class="h-4 bg-amber-100 rounded"></div>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="historicoPorCodigo.length === 0" class="text-gray-500">Nenhuma consulta por código realizada ainda.</div>
                        <div v-else class="flex flex-col gap-4">
                            <div v-for="item in historicoFiltradoPorCodigo" :key="item.id" class="rounded-xl border border-yellow-100 bg-amber-50 p-4 flex flex-col gap-2 shadow-sm hover:shadow-md transition-all">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Busca por Código
                                    </span>
                                    <span class="text-xs text-gray-500">{{ new Date(item.created_at).toLocaleDateString('pt-BR') }}</span>
                                </div>
                                
                                <div class="flex-1 flex flex-col md:flex-row md:items-center gap-2">
                                    <div class="flex flex-col min-w-[120px]">
                                        <span class="text-xs text-gray-500">Código</span>
                                        <span class="font-bold text-yellow-900">{{ item.codigo_imovel || '-' }}</span>
                                    </div>
                                    <div class="flex flex-col min-w-[120px]">
                                        <span class="text-xs text-gray-500">Documento</span>
                                        <span class="font-bold text-yellow-900">{{ item.resposta_api.titulares?.[0]?.cpfCnpj || '-' }}</span>
                                    </div>
                                    <div class="flex flex-col min-w-[160px]">
                                        <span class="text-xs text-gray-500">Nome Titular</span>
                                        <span class="font-semibold text-gray-900">{{ item.resposta_api.titulares?.[0]?.nomeTitular || '-' }}</span>
                                    </div>
                                    <div class="flex flex-col min-w-[120px]">
                                        <span class="text-xs text-gray-500">Município</span>
                                        <span class="text-gray-800">{{ item.resposta_api.municipioSede || '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 mt-3 sm:justify-end">
                                    <button @click="mostrarDetalhe = item" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-full transition-all duration-200" title="Ver Detalhes">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <a :href="route('customer.exportar-consulta-imovel-pdf', item.id)" target="_blank" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-full transition-all duration-200" title="Baixar PDF">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Botão Ver Mais -->
                        <div v-if="temMaisItensCodigo" class="mt-4 flex justify-center">
                            <button 
                                @click="carregarMaisItens" 
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-yellow-700 bg-yellow-100 border border-yellow-300 rounded-lg hover:bg-yellow-200 hover:text-yellow-800 transition-all duration-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                Ver Mais
                            </button>
                        </div>
                        
                        <!-- Modal/Detalhe -->
                        <div style="margin-bottom:100px;" v-if="mostrarDetalhe" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                            <div class="bg-white rounded-lg p-6 max-w-2xl w-full relative">
                                <button @click="mostrarDetalhe = null" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">&times;</button>
                                <h4 class="text-lg font-bold mb-2">
                                    {{ abaAtiva === 'ni' ? 'Códigos Encontrados' : 'Detalhes da Consulta' }}
                                </h4>
                                <!-- Para consultas por código -->
                                <template v-if="abaAtiva === 'codigo'">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div><span class="font-medium">Código INCRA:</span> {{ mostrarDetalhe.resposta_api.codigoImovelIncra }}</div>
                                        <div><span class="font-medium">Denominação:</span> {{ mostrarDetalhe.resposta_api.denominacao }}</div>
                                        <div><span class="font-medium">Área Total:</span> {{ mostrarDetalhe.resposta_api.areaTotal }} ha</div>
                                        <div><span class="font-medium">Classificação:</span> {{ mostrarDetalhe.resposta_api.classificacaoFundiaria }}</div>
                                        <div><span class="font-medium">Município:</span> {{ mostrarDetalhe.resposta_api.municipioSede }}</div>
                                        <div><span class="font-medium">UF:</span> {{ mostrarDetalhe.resposta_api.ufSede }}</div>
                                        <div><span class="font-medium">Data Última Declaração:</span> {{ mostrarDetalhe.resposta_api.dataProcessamentoUltimaDeclaracao }}</div>
                                    </div>
                                </template>
                                
                                <!-- Para consultas por NI -->
                                <template v-if="abaAtiva === 'ni'">
                                    <div class="mb-4">
                                        <div class="text-sm text-gray-600 mb-2">
                                            <strong>NI Consultado:</strong> {{ mostrarDetalhe.ni_consultado }}
                                        </div>
                                        <div class="text-sm text-gray-600 mb-4">
                                            <strong>Total de imóveis encontrados:</strong> {{ mostrarDetalhe.resposta_api.length }}
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h5 class="font-semibold text-gray-900 mb-3">Códigos dos Imóveis:</h5>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                            <div 
                                                v-for="codigo in mostrarDetalhe.resposta_api" 
                                                :key="codigo"
                                                class="bg-white rounded border p-2 text-center cursor-pointer hover:bg-blue-50 transition-colors"
                                                @click="consultarCodigoEncontrado(codigo); mostrarDetalhe = null"
                                            >
                                                <span class="font-mono text-sm text-blue-800">{{ codigo }}</span>
                                                <div class="text-xs text-blue-600 mt-1">Clique para consultar</div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Detalhes adicionais apenas para consultas por código -->
                                <template v-if="abaAtiva === 'codigo'">
                                    <div v-if="mostrarDetalhe.resposta_api.titulares && mostrarDetalhe.resposta_api.titulares.length" class="mt-4">
                                        <h4 class="font-semibold mb-1">Titulares:</h4>
                                        <ul class="list-disc ml-6">
                                            <li v-for="tit in mostrarDetalhe.resposta_api.titulares" :key="tit.cpfCnpj">
                                                {{ tit.nomeTitular }} ({{ tit.cpfCnpj }}) - {{ tit.condicaoTitularidade }}
                                            </li>
                                        </ul>
                                    </div>
                                    <div v-if="mostrarDetalhe.resposta_api.areasRegistradas && mostrarDetalhe.resposta_api.areasRegistradas.length" class="mt-4">
                                        <h4 class="font-semibold mb-1">Áreas Registradas:</h4>
                                        <ul class="list-disc ml-6">
                                            <li v-for="area in mostrarDetalhe.resposta_api.areasRegistradas" :key="area.matriculaOuTranscricao">
                                                {{ area.municipioCartorio }}/{{ area.ufCartorio }} - Matrícula: {{ area.matriculaOuTranscricao }} - Área: {{ area.area }} ha
                                            </li>
                                        </ul>
                                    </div>
                                    <div v-if="mostrarDetalhe.resposta_api.dadosUltimoCcir" class="mt-4">
                                        <h4 class="font-semibold mb-1">Dados Último CCIR:</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                            <div><span class="font-medium">Nº CCIR:</span> {{ mostrarDetalhe.resposta_api.dadosUltimoCcir.numeroCcir }}</div>
                                            <div><span class="font-medium">Situação:</span> {{ mostrarDetalhe.resposta_api.dadosUltimoCcir.situacaoCcir }}</div>
                                            <div><span class="font-medium">Valor Total:</span> R$ {{ mostrarDetalhe.resposta_api.dadosUltimoCcir.valorTotal }}</div>
                                            <div><span class="font-medium">Data Geração:</span> {{ mostrarDetalhe.resposta_api.dadosUltimoCcir.dataGeracaoCcir }}</div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="mt-6 grid grid-cols-1 gap-4 sm:gap-5 sm:grid-cols-2">
                    <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-gray-100 transition-all duration-200 hover:shadow-md">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-gray-100 rounded-xl p-3">
                                    <svg class="h-6 w-6 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Total de Pedidos
                                        </dt>
                                        <dd class="flex items-baseline">
                                            <div class="text-2xl font-semibold text-gray-900">
                                                {{ ordersCount }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <Link
                                    :href="route('customer.orders')"
                                    class="font-medium text-gray-900 hover:text-gray-700 transition-colors duration-200"
                                >
                                    Meus Pedidos
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden rounded-2xl shadow-sm border border-gray-100 transition-all duration-200 hover:shadow-md">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-gray-100 rounded-xl p-3">
                                    <svg class="h-6 w-6 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Itens no Carrinho
                                        </dt>
                                        <dd class="flex items-baseline">
                                            <div class="text-2xl font-semibold text-gray-900">
                                                {{ cartItemsCount }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-5 py-3">
                            <div class="text-sm">
                                <Link
                                    :href="route('cart.index')"
                                    class="font-medium text-gray-900 hover:text-gray-700 transition-colors duration-200"
                                >
                                    Ver Carrinho
                                </Link>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </CustomerDashboardLayout>
    </BottomNavLayout>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
