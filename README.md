# ChatDolar — Contexto de Integração Loja + Chat

## Stack
- **Frontend:** React + Vite + TypeScript
- **Backend:** Laravel 12 + Reverb (WebSockets) + MySQL
- **Loja:** HTML + CSS + JS puro (sem framework)

---

## Arquitectura Geral

O sistema tem duas interfaces distintas:

1. **Loja** (`chat-dolar-store/`) — vitrine de produtos estática
2. **Chat** (`chat-dolar/`) — app React com painel admin e chat de visitante

---

## Sellers (Tenants)

Cada seller tem um `tenant_id` único. A loja é partilhada mas os chats são separados por tenant.

| Seller | Tenant ID |
|--------|-----------|
| Kodan  | `b094d1e2-37f7-4039-abe0-6f525a3722c2` |
| Phoenix | `775cff0c-a752-4bf1-abdb-886343f9a1dc` |

---

## URLs de Acesso

| Quem | URL |
|------|-----|
| Admin login | `http://192.168.1.100:5173/login` |
| Admin painel (leads) | `http://192.168.1.100:5173/leads` |
| Loja Kodan | `http://192.168.1.100:5500/index.html?seller=kodan` |
| Loja Phoenix | `http://192.168.1.100:5500/index.html?seller=phoenix` |

---

## Fluxo Completo

### 1. Visitante acede à loja
- URL contém `?seller=kodan` ou `?seller=phoenix`
- O `config.js` resolve o `TENANT_ID` a partir do seller
- Os produtos são exibidos em grid estilo Netflix com filtro e pesquisa

### 2. Visitante clica em "Buy Now"
- Redireccionado para o chat com os parâmetros:
  ```
  http://192.168.1.100:5173/chat?t=TENANT_ID&productId=PUBLIC_ID
  ```
- `t` = tenant_id do seller
- `productId` = `public_id` do produto (string aleatória pública, ex: `AGsjGAjskalsksl`)

### 3. Chat arranca automaticamente
- Username gerado automaticamente no formato `Animal4321` (ex: `Panda4821`)
- Sem modal de nome — conversa inicia imediatamente
- Username guardado no `sessionStorage` por tenant

### 4. Mensagens automáticas injectadas pelo backend
Ao criar a conversa, o `StartConversationAction` injeta automaticamente:

**Mensagem 1 — do visitante:**
```
Hi! I want to buy: FIFA 26 PC ($35)
```

**Mensagem 2 — do bot (aparece como admin):**
```
Hello! To complete your purchase, please choose one of the payment options below:

Option 1 - Crypto (Binance)
Send $35 to the following BTC address:
1MPa1fSFYYWADLC7xvZpXh5VM1mDp2mjGD

Option 2 - Gift Card via Eneba
Purchase a $35 gift card here:
https://www.eneba.com/store/gift-cards?q=35+usd

Option 3 - Gift Card via G2A
Purchase a $35 gift card here:
https://www.g2a.com/search?query=35+usd+gift+card

Once payment is done, please send us the receipt or proof of payment in this chat and we will process your order right away.
```

### 5. Admin recebe a conversa em tempo real
- O `LeadsPage` subscreve o canal WebSocket `tenant.{tenantId}`
- Ao criar conversa, o backend dispara `ConversationCreated` nesse canal
- A lista de leads actualiza automaticamente sem refresh

---

## Produtos

Guardados na tabela `products` com dois IDs:

| Campo | Descrição |
|-------|-----------|
| `id` | UUID interno — nunca exposto |
| `public_id` | String aleatória pública — usado na loja e nas URLs |
| `visual_name` | Nome visível no painel admin |
| `price` | Preço em USD |
| `tenant_id` | UUID do tenant ao qual pertence |

### Produtos actuais (tenant Phoenix `775cff0c...`)

| public_id | Nome | Preço |
|-----------|------|-------|
| `AGsjGAjskalsksl` | FIFA 26 PC | $35 |
| `GSsksksueieoesj` | EA FC 25 PC | $35 |
| `KjHTSGSksskagg` | Call of Duty MW III | $35 |
| `MnBvCxZwQrStYpLk` | Hogwarts Legacy PC | $35 |
| `TzPmXwRqLsNvBcJy` | GTA V Premium | $20 |
| `HdKfYnCgWbSxQrMp` | Red Dead Redemption 2 | $20 |
| `VjZtNsLmRkXqPwGh` | Cyberpunk 2077 | $20 |
| `WrPkTnBsXmCqLzFj` | Elden Ring PC | $20 |
| `BcFrYpDsKwNqMxTv` | Minecraft Java | $10 |
| `QmJsXtLzWcRgNkPb` | Roblox Premium Account | $10 |
| `DhSnVfYqGwBmKtCx` | Among Us + DLCs | $10 |
| `JkRmPwBtXsNcLqYz` | Fall Guys Premium | $10 |

---

## Ficheiros Chave

### Backend
```
app/Actions/Chat/StartConversationAction.php  — cria conversa + injeta msgs automáticas
app/Events/ConversationCreated.php            — broadcast ao criar conversa
app/Events/MessageSent.php                    — broadcast de cada mensagem
app/Models/Product.php                        — model com HasUuids
app/Http/Controllers/Api/ProductController.php — GET /products/{publicId}?tenant_id=
database/seeders/ProductSeeder.php
```

### Frontend (React)
```
src/pages/chat/ChatPage.tsx          — chat do visitante + admin
src/pages/leads/LeadsPage.tsx        — lista de conversas em tempo real
src/utils/visitorUsername.ts         — gerador de username Animal4321
src/components/leads/LeadCard.tsx    — card de conversa com badge de não lidas
```

### Loja (HTML puro)
```
chat-dolar-store/
├── index.html    — estrutura + age gate
├── store.css     — estilos dark theme estilo Netflix
├── store.js      — lógica, produtos, filtros, carousel
└── config.js     — ENV: BASE_URL, CHAT_PORT, SELLERS, DEFAULT_SELLER
```

---

## config.js da Loja

```js
window.ENV = {
  BASE_URL:  'http://192.168.1.100',  // dev | 'https://seudominio.com' prod
  CHAT_PORT: '5173',                  // dev | '' prod (sem porta)
  SELLERS: {
    kodan:   'b094d1e2-37f7-4039-abe0-6f525a3722c2',
    phoenix: '775cff0c-a752-4bf1-abdb-886343f9a1dc',
  },
  DEFAULT_SELLER: 'kodan',
};
```