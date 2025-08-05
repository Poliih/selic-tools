
# 📊 SelicTools

SelicTools é um painel interativo e simulador de rendimento com base na **Taxa Selic**, utilizando dados diretamente da **API oficial de Dados Abertos do Banco Central do Brasil (BCB)**.

A ferramenta combina um frontend moderno em **Vue 3 + Inertia.js** com backend robusto em **Laravel**, oferecendo visualizações dinâmicas, simulações financeiras, presets de datas e exportação em CSV.

---

## 📡 Fonte Oficial dos Dados

Este projeto consome a API pública vinculada à seguinte base oficial:

**🗂 Dataset: Taxa de Juros – Selic (Código 11)**  
🔗 [https://dadosabertos.bcb.gov.br/dataset/11-taxa-de-juros---selic](https://dadosabertos.bcb.gov.br/dataset/11-taxa-de-juros---selic)

- Os dados vêm da **Série Temporal SGS** do BCB
- API pública baseada em HTTP (sem autenticação)
- Formato de resposta: JSON
- Limite de período: até 10 anos por requisição

Exemplo de endpoint usado:
```

[https://api.bcb.gov.br/dados/serie/bcdata.sgs.11/dados?formato=json\&dataInicial=01/01/2024\&dataFinal=31/07/2024](https://api.bcb.gov.br/dados/serie/bcdata.sgs.11/dados?formato=json&dataInicial=01/01/2024&dataFinal=31/07/2024)

````

---

## 🚀 Funcionalidades

✅ Consulta histórica da Selic com média mensal  
✅ Simulação de rendimento diário com base nos dados oficiais  
✅ Interface elegante e responsiva (desktop/mobile)  
✅ Exportação de dados para CSV  
✅ Presets rápidos: últimos 30 dias, 3 meses, 6 meses ou 1 ano  
✅ Validação de datas, carregamento dinâmico e mensagens de erro

---

## 🖥️ Tecnologias Utilizadas

**Frontend:**
- Vue 3 + Composition API
- Inertia.js
- Chart.js
- Tailwind CSS
- Axios

**Backend:**
- Laravel 10+
- PHP 8.1+
- SQLite (ou qualquer outro banco)
- Serviço de exportação em CSV

---

## 🔧 Instalação

1. Clone o repositório

```bash
git clone https://github.com/Poliih/selic-tools.git
cd selic-tools
````

2. Configure o ambiente Laravel

```bash
composer install
cp .env.example .env
php artisan key:generate

# Use SQLite (por padrão)
touch database/database.sqlite
# Edite o .env:
# DB_CONNECTION=sqlite

php artisan serve
```

3. Instale as dependências do frontend

```bash
npm install
npm run dev
```

---

## 📡 Endpoints da API Interna

| Método | Rota             | Função                                |
| ------ | ---------------- | ------------------------------------- |
| GET    | `/api/selic`     | Consulta Selic no intervalo informado |
| GET    | `/api/simulacao` | Simula rendimento com base na Selic   |
| GET    | `/api/exportar`  | Exporta dados da consulta para CSV    |

**Parâmetros esperados:**

* `startDate` e `endDate` (no formato DD/MM/AAAA)
* `valor` (para simulação)

---

## 🧪 Exemplo de Uso

### Simulação de rendimento:

```http
GET /api/simulacao?valor=1000&startDate=01/01/2024&endDate=31/07/2024
```

### Exportar CSV:

```http
GET /api/exportar?startDate=01/01/2024&endDate=31/07/2024
```

