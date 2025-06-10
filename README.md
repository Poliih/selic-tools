-----

# Selic Tools 📊

Este é um projeto de aplicação web full-stack que utiliza o **Laravel** para o backend e **Vue.js** para o frontend. A aplicação consome a API de Séries Temporais (SGS) do Banco Central do Brasil para buscar, exibir, simular e exportar dados da taxa Selic.

-----

## ✨ Funcionalidades

  * **Dashboard Interativo:** Visualização do histórico da taxa Selic com um gráfico dinâmico.
  * **Seleção de Período:** Permite ao usuário escolher períodos predefinidos (ex: 30 dias, 3 meses, 1 ano) ou selecionar datas de início e fim manualmente.
  * **Simulador de Rendimentos:** Calcula o valor final de um investimento inicial com base na variação da Selic no período informado.
  * **Exportação de Dados:** Permite baixar os dados do período selecionado em formato **CSV**.
  * **Interface Reativa:** Interface de usuário moderna e responsiva construída com Vue.js e Tailwind CSS.

-----

## 🛠️ Tecnologias Utilizadas

  * **Backend:**
      * PHP 8+
      * **Laravel 10+**: Framework principal do backend.
      * **Guzzle (via `Http` Facade)**: Para fazer as chamadas à API do Banco Central.
  * **Frontend:**
      * **Vue.js 3** (com Composition API e `<script setup>`)
      * **Vite**: Ferramenta de build do frontend.
      * **Axios**: Para as requisições HTTP do cliente para o backend.
      * **Chart.js**: Para a renderização do gráfico de histórico.
      * **Tailwind CSS**: Para a estilização da interface.
  * **API Externa:**
      * [API de Séries Temporais (SGS)](https://dadosabertos.bcb.gov.br/dataset/11-taxa-de-juros---selic) do Banco Central do Brasil.

-----

## 🏗️ Arquitetura do Projeto

O projeto é dividido em duas partes principais: o backend Laravel, que serve uma API interna, e o frontend Vue.js, que consome essa API.

### Backend (Laravel)

O backend é responsável por toda a lógica de negócio e pela comunicação com a API do Banco Central.

#### **1. `SelicService.php`**

  * **Responsabilidade:** Camada de serviço que encapsula a lógica de comunicação com a API do Banco Central. Isso mantém o controller limpo e a lógica de acesso a dados externos isolada.
  * **Métodos Principais:**
      * `getSelicData($startDate, $endDate)`: Busca os dados da Selic em um intervalo de datas.
      * `getLatestSelic()`: Busca o último registro da taxa Selic.

#### **2. `SelicController.php`**

  * **Responsabilidade:** Orquestrar as requisições HTTP, chamar o `SelicService` para buscar os dados e formatar a resposta para o frontend.
  * **Métodos (Endpoints):**
      * `historico()`: Retorna uma lista de registros da Selic para um determinado período. Usado pelo Dashboard.
      * `simular()`: Recebe um valor inicial e um período, calcula o rendimento com base nas taxas diárias da Selic e retorna o valor final.
      * `exportar()`: Busca os dados de um período e os formata em uma string CSV, retornando um arquivo para download.

### Frontend (Vue.js)

O frontend é composto por componentes Vue que gerenciam o estado da interface e a interação do usuário.

#### **1. Componente de Dashboard**

  * **Responsabilidade:** Exibir o histórico e o gráfico da Selic.
  * **Lógica:**
      * Utiliza `ref` para gerenciar o estado das datas (`startDate`, `endDate`), dos dados (`dados`) e do estado de carregamento (`isLoading`).
      * Usa um `watch` com *debounce* para chamar a API automaticamente sempre que as datas mudam, evitando chamadas excessivas.
      * O método `buscarSelic` usa **Axios** para fazer uma requisição ao endpoint `/api/selic` do backend.
      * Após receber os dados, agrupa-os por mês e renderiza o gráfico com a biblioteca **Chart.js**.

#### **2. Componente de Simulador**

  * **Responsabilidade:** Fornecer a interface para a simulação de rendimentos.
  * **Lógica:**
      * Gerencia o estado dos inputs: valor inicial, data de início e data de fim.
      * Ao submeter o formulário, o método `simular` chama o endpoint `/api/simulacao` do backend via **Axios**.
      * Exibe o resultado (`valorfinal`) retornado pelo backend ou uma mensagem de erro.

-----

## ⚙️ Como Executar o Projeto

1.  **Clone o repositório:**

    ```bash
    git clone https://github.com/Poliih/selic-tools.git
    cd selic-tools
    ```

2.  **Instale as dependências do Backend:**

    ```bash
    composer install
    ```

3.  **Configure o ambiente:**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Instale as dependências do Frontend:**

    ```bash
    npm install
    ```

5.  **Compile os assets do Frontend:**

    ```bash
    # Para desenvolvimento (com hot-reload)
    npm run dev

    # Para produção
    npm run build
    ```

6.  **Inicie o servidor de desenvolvimento:**

    ```bash
    php artisan serve
    ```

    A aplicação estará disponível em `http://127.0.0.1:8000`.

-----

## 🔌 Endpoints da API Interna

O backend Laravel expõe os seguintes endpoints para o frontend:

  * `GET /api/selic?startDate={dd/mm/yyyy}&endDate={dd/mm/yyyy}`

      * Retorna o histórico da Selic no período.

  * `GET /api/simulacao?valor={float}&startDate={dd/mm/yyyy}&endDate={dd/mm/yyyy}`

      * Retorna o resultado da simulação de rendimento.

  * `GET /api/exportar?startDate={dd/mm/yyyy}&endDate={dd/mm/yyyy}`

      * Inicia o download de um arquivo `selic.csv` com os dados do período.
