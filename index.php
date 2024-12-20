<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Simulador de Crédito</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="style.css">
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap"
      rel="stylesheet"
    />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
      $(document).ready(function () {
        // Aplicar máscara de moeda nos campos
        $("#amount-text, #entry-value").mask("R$ ###.###.###,00", {
          reverse: true,
        });
      });
    </script>
  </head>
  <body>
    <header>
      <img src="images/VFBANK.png" alt="Logo" class="logo" />
      <nav>
        <a href="#">Simulador</a>
        <a href="#" class="highlight">Crédito Pessoal</a>
      </nav>
    </header>
    <div class="container">
      <main>
        <div class="esquerdo">
          <h2>Simule seu Crédito</h2>
          <section id="step-1" class="simulator step">
            <div class="input-group">
              <div class="box">
                <label for="client-name">Nome do Cliente <span style="color: red;">*</span></label>
              </div>
              <div class="input-wrapper">
                <input type="text" id="client-name" placeholder="Digite seu nome completo" />
              </div>
            </div>

            <div class="input-group">
              <div class="box">
                <label for="client-phone">Telefone <span style="color: red;">*</span></label>
              </div>
              <div class="input-wrapper">
                <input type="text" id="client-phone" placeholder="Digite seu número de contato" />
              </div>
            </div>

            <div class="input-group">
              <div class="box">
                <label for="client-email">E-mail <span style="color: red;">*</span></label>
              </div>
              <div class="input-wrapper">
                <input type="email" id="client-email" placeholder="Digite seu melhor e-mail" />
              </div>
            </div>
            <div class="btns-step1">
              <button class="next-btn" onclick="goToStep(2)">Próximo</button>
            </div>
          </section>

          <section id="step-2" class="simulator step" style="display: none;">
            <div class="input-group">
              <div class="box">
                <label for="amount-text">Qual o valor que deseja solicitar? <span style="color: red;">*</span></label>
              </div>
              <div class="input-wrapper">
                <span class="currency-prefix">R$</span>
                <input type="text" id="amount-text" maxlength="20" placeholder="0,00" />
              </div>
            </div>

            <div class="input-group">
              <div class="box">
                <label for="entry-value">Informe o valor da entrada? <span style="color: red;">*</span></label>
              </div>
              <div class="input-wrapper">
                <span class="currency-prefix">R$</span>
                <input type="text" id="entry-value" placeholder="0,00" />
              </div>
            </div>

            <div class="btns">
              <button class="prev-btn" onclick="goToStep(1)">Voltar</button>
              <button class="next-btn" onclick="goToStep(3)">Próximo</button>
            </div>
          </section>

          <section id="step-3" class="simulator step" style="display: none;">
            <div class="input-group">
              <div class="box">
                <label for="anticipate-month">Em qual mês você deseja obter o crédito? <span style="color: red;">*</span></label>
              </div>
              <select id="anticipate-month">
                <option value="#" selected>Selecione o mês</option>
                <option value="3">3º mês</option>
                <option value="5">5º mês</option>
                <option value="6">6º mês</option>
                <option value="12">12º mês</option>
              </select>
            </div>

            <div class="input-group">
              <div class="box">
                <label for="months-select">Escolha o prazo para pagamento <span style="color: red;">*</span></label>
              </div>
              <select id="months-select">
                <option value="#" selected>Selecione um prazo</option>
                <option value="12">12 meses</option>
                <option value="24">24 meses</option>
                <option value="36">36 meses</option>
                <option value="48">48 meses</option>
                <option value="60">60 meses</option>
              </select>
            </div>

            <div class="input-group">
              <div class="box">
                <label for="start-date">Defina a data do primeiro vencimento <span style="color: red;">*</span></label>
              </div>
              <input type="date" id="start-date" />
            </div>

            <div class="btns">
              <button class="prev-btn" onclick="goToStep(2)">Voltar</button>
              <button class="download-btn" onclick="openPopup(); updateSimulation();">Simule Já</button>
            </div>
          </section>
        </div>
      </main>
    </div>

    <!-- Pop-up de download/impressão -->
    <div class="popup-overlay" id="popupOverlay">
      <div class="popup-content">
        <span class="close-popup" onclick="closePopup()">×</span>
        <h2>Finalizar Simulação</h2>
        <div class="popup-buttons">
          <div class="box-btn">
            <button class="popup-button" onclick="downloadSimulationPDF()">Baixar</button>
            <p class="popup-message">Baixe seu PDF clicando no botão acima.</p>
          </div>
          <div class="box-btn">
            <button class="popup-button" onclick="printSimulationPDF()">Imprimir</button>
            <p class="popup-message">Imprima o resultado clicando no botão acima.</p>
          </div>
        </div>
      </div>
    </div>

 <script>
function goToStep(step) {
  document.querySelectorAll('.step').forEach((section) => {
    section.style.display = 'none';
  });
  document.getElementById(`step-${step}`).style.display = 'block';
}

function openPopup() {
  document.getElementById("popupOverlay").style.display = "block";
}

function closePopup() {
  document.getElementById("popupOverlay").style.display = "none";
}

async function fetchTaxa(mes, parcelas) {
  try {
    const response = await fetch(`getTaxas.php?mes=${mes}&parcelas=${parcelas}`);
    const data = await response.json();

    if (data.taxa) {
      return parseFloat(data.taxa); // Retorna a taxa como número
    } else {
      console.error("Erro ao buscar a taxa:", data.error);
      return null; // Retorna null se a taxa não for encontrada
    }
  } catch (error) {
    console.error("Erro na requisição:", error);
    return null;
  }
}


async function updateSimulation() {
  // Obter valores do formulário e formatar
  const amount = parseFloat(document.getElementById("amount-text").value.replace(/[R$\.\s]/g, "").replace(",", ".")) || 0;
  const entryValue = parseFloat(document.getElementById("entry-value").value.replace(/[R$\.\s]/g, "").replace(",", ".")) || 0;
  const monthsSelectValue = document.getElementById("months-select").value;
  const anticipateMonthValue = document.getElementById("anticipate-month").value;

  const months = parseInt(monthsSelectValue) > 0 ? parseInt(monthsSelectValue) : 0;
  const anticipateMonth = parseInt(anticipateMonthValue) > 0 ? parseInt(anticipateMonthValue) : 0;

  // Validações iniciais
  if (months === 0 || anticipateMonth === 0) {
    console.warn("Por favor, selecione um prazo e um mês para antecipação.");
    return;
  }

  // Buscar a taxa de juros do backend
  const baseInterestRate = await fetchTaxa(anticipateMonth, months);

  if (!baseInterestRate) {
    console.warn("Não foi possível obter a taxa de juros. Verifique os parâmetros.");
    return;
  }

  // Aplicar a taxa fixa de 4% ao valor do financiamento
  const adjustedAmount = amount * (1 + 0.04);

  // Verificar valores intermediários
  if (isNaN(adjustedAmount) || adjustedAmount <= 0) {
    console.error("Erro: Valor ajustado inválido.");
    return;
  }

  // Calcular o valor total com juros simples
  const totalWithInterest = adjustedAmount + (adjustedAmount * baseInterestRate * months);

  if (isNaN(totalWithInterest) || totalWithInterest <= 0) {
    console.error("Erro: Valor total com juros inválido.");
    return;
  }

  // Calcular parcela mensal
  const monthlyPayment = months > 0 ? roundToTwo(totalWithInterest / months) : 0;

  if (isNaN(monthlyPayment) || monthlyPayment <= 0) {
    console.error("Erro: O cálculo da parcela mensal resultou em um valor inválido.");
    return;
  }

  // Calcular a adesão (primeira parcela)
  const adesaoValue = monthlyPayment;

  console.log(`Taxa aplicada: ${baseInterestRate}`);
  console.log(`Total com juros: ${totalWithInterest}`);
  console.log(`Parcela mensal: ${monthlyPayment}`);
  console.log(`Adesão: ${adesaoValue}`);

  // Gerar tabela de simulação
  const tableData = generateTableData(
    adjustedAmount,
    entryValue,
    monthlyPayment,
    anticipateMonth,
    months,
    totalWithInterest
  );

  console.log("Tabela de simulação gerada:", tableData);

  // Salvar dados para geração de PDF ou impressão
  window.simulationData = {
    clientName: document.getElementById("client-name").value,
    clientPhone: document.getElementById("client-phone").value,
    clientEmail: document.getElementById("client-email").value,
    amount: amount,
    adesaoValue: adesaoValue, // Adicionando adesão no cabeçalho
    entryValue: entryValue,
    months: months,
    anticipateMonth: anticipateMonth,
    interestRate: baseInterestRate,
    adjustedAmount: adjustedAmount,
    tableData: tableData,
  };
}

function addMonths(date, months) {
  const result = new Date(date);
  result.setMonth(result.getMonth() + months);
  return result;
}

function generateTableData(adjustedAmount, entryValue, monthlyPayment, anticipateMonth, months, totalWithInterest) {
  const tableData = [];
  const startDate = document.getElementById("start-date").value;
  const startDateObj = new Date(startDate);

  // Adicionar a adesão como a primeira parcela
  const adesaoValue = isNaN(monthlyPayment) || monthlyPayment <= 0 ? 0 : monthlyPayment;
  tableData.push([
    "Adesão",
    formatDate(startDateObj),
    formatCurrency(adesaoValue),
    "-",
    formatCurrency(adesaoValue),
  ]);

  let saldoAtual = totalWithInterest - adesaoValue;

  startDateObj.setMonth(startDateObj.getMonth() + 1);

  // Adicionar parcelas até o mês do aporte
  for (let i = 1; i < anticipateMonth; i++) {
    saldoAtual -= monthlyPayment;
    tableData.push([
      i,
      formatDate(addMonths(startDateObj, i - 1)),
      formatCurrency(0),
      formatCurrency(monthlyPayment),
      formatCurrency(monthlyPayment),
    ]);
  }

  // Aplicar o aporte
  saldoAtual -= entryValue;
  tableData.push([
    anticipateMonth,
    formatDate(addMonths(startDateObj, anticipateMonth - 1)),
    formatCurrency(entryValue),
    formatCurrency(monthlyPayment),
    formatCurrency(monthlyPayment),
  ]);

  // Recalcular parcelas restantes
  const remainingMonths = months - anticipateMonth;
  const newMonthlyPayment = remainingMonths > 0 ? roundToTwo(saldoAtual / remainingMonths) : 0;

  for (let i = anticipateMonth + 1; i <= months; i++) {
    saldoAtual -= newMonthlyPayment;
    tableData.push([
      i,
      formatDate(addMonths(startDateObj, i - 1)),
      formatCurrency(0),
      formatCurrency(newMonthlyPayment),
      formatCurrency(newMonthlyPayment),
    ]);
  }

  return tableData;
}


function roundToTwo(value) {
  return Math.round(value * 100) / 100;
}

function formatCurrency(value) {
  return parseFloat(value).toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
  });
}

function formatDate(date) {
  return date.toLocaleDateString("pt-BR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
}




function generatePDFContent() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  const data = window.simulationData;
  if (!data) {
    alert("Por favor, preencha todos os campos e gere a simulação antes de baixar o PDF.");
    return null;
  }

  const {
    clientName,
    clientPhone,
    clientEmail,
    amount,
    adesaoValue,
    entryValue,
    months,
    anticipateMonth,
    interestRate,
    tableData,
  } = data;

  const logoUrl = "images/VFBANK.png";
  doc.addImage(logoUrl, "PNG", 80, 10, 50, 30);

  doc.setFillColor(255, 204, 0);
  doc.rect(10, 50, 190, 10, "F");

  doc.setFont("Helvetica", "bold");
  doc.setFontSize(14);
  doc.setTextColor(255, 255, 255);
  doc.text("Simulação de Crédito", 105, 57, null, null, "center");

  const parametersTable = [
    ["Nome do Cliente", clientName],
    ["Telefone", clientPhone],
    ["E-mail", clientEmail],
    ["Crédito", formatCurrency(amount)],
    ["Adesão", formatCurrency(adesaoValue)],
    ["Entrada/Aporte", formatCurrency(entryValue)],
    ["Prazo (Meses)", `${months}`],
    ["Taxa de Juros", `${(interestRate * 100).toFixed(2)}%`], // Mostra a taxa base no PDF
    ["Antecipar Mês", anticipateMonth ? `${anticipateMonth}º mês` : "0"],
  ];

  doc.autoTable({
    startY: 65,
    head: [["PARÂMETROS", "VALORES"]],
    body: parametersTable,
    styles: { fontSize: 10, cellPadding: 5 },
    headStyles: { fillColor: [255, 204, 0], textColor: 0 },
    alternateRowStyles: { fillColor: [245, 245, 245] },
  });

  doc.autoTable({
    startY: doc.lastAutoTable.finalY + 10,
    head: [
      [
        "PARC",
        "VENCIMENTO",
        "ENTRADA/APORTE",
        "PARCELA",
        "PAGAMENTO",
      ],
    ],
    body: tableData,
    styles: { fontSize: 10, cellPadding: 4 },
    headStyles: { fillColor: [0, 0, 0], textColor: 255 },
    alternateRowStyles: { fillColor: [240, 240, 240] },
  });

  return doc;
}

function printSimulationPDF() {
  const doc = generatePDFContent();
  if (doc) {
    doc.autoPrint();
    window.open(doc.output('bloburl'), '_blank');
  }
}

function downloadSimulationPDF() {
  const doc = generatePDFContent();
  if (doc) {
    doc.save("simulacao_credito.pdf");
  }
}

// Adicionar eventos de atualização
document.getElementById("amount-text").addEventListener("change", updateSimulation);
document.getElementById("entry-value").addEventListener("change", updateSimulation);
document.getElementById("months-select").addEventListener("change", updateSimulation);
document.getElementById("anticipate-month").addEventListener("change", updateSimulation);
document.getElementById("start-date").addEventListener("change", updateSimulation);

 </script>
  </body>
</html>