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

function updateSimulation() {
  const amount = parseFloat(document.getElementById("amount-text").value.replace(/[R$\.\s]/g, "").replace(",", ".")) || 0;
  const entryValue = parseFloat(document.getElementById("entry-value").value.replace(/[R$\.\s]/g, "").replace(",", ".")) || 0;
  const monthsSelectValue = document.getElementById("months-select").value;
  const anticipateMonthValue = document.getElementById("anticipate-month").value;

  const months = parseInt(monthsSelectValue) > 0 ? parseInt(monthsSelectValue) : 0;
  const anticipateMonth = parseInt(anticipateMonthValue) > 0 ? parseInt(anticipateMonthValue) : 0;

  if (months === 0 || anticipateMonth === 0) {
    console.warn("Por favor, selecione um prazo e um mês para antecipação.");
    return;
  }

  // Determinar a taxa de juros com base nas regras
  let interestRate = 0;
  if (anticipateMonth === 3 && [24, 36, 48].includes(months)) {
    interestRate = 0.0199; // 1,99% ao mês
  } else if (anticipateMonth === 5 && [24, 36, 48].includes(months)) {
    interestRate = 0.0179; // 1,79% ao mês
  } else if (anticipateMonth === 6 && [24, 36, 48].includes(months)) {
    interestRate = 0.0169; // 1,69% ao mês
  } else if ([3, 5, 6].includes(anticipateMonth) && months === 12) {
    interestRate = 0.0245; // 2,45% ao mês
  } else if (anticipateMonth === 12 && months === 60) {
    interestRate = 0.0149; // 1,49% ao mês
  } else {
    interestRate = 0.0199; // Taxa fixa de 1,99% ao mês
  }

  console.log("Taxa de juros aplicada: ", interestRate);

  // Atualizar o valor total com juros
  let totalWithInterest = amount + amount * interestRate;
  console.log(`Valor total com juros: ${totalWithInterest.toFixed(2)}`);

  // Calcular parcela mensal baseada no valor total com juros
  let monthlyPayment = (totalWithInterest / months).toFixed(2);
  console.log(`Parcela mensal inicial: ${monthlyPayment}`);

  const tableData = [];
  const startDate = document.getElementById("start-date").value;
  const startDateObj = new Date(startDate);

  let saldoAtual = totalWithInterest;

  // Adicionar parcelas até o mês do aporte
  for (let i = 1; i < anticipateMonth; i++) {
    const dueDate = new Date(startDateObj);
    dueDate.setMonth(startDateObj.getMonth() + (i - 1));
    const formattedDate = dueDate.toLocaleDateString("pt-BR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });

    saldoAtual -= monthlyPayment;
    tableData.push([
      i,
      formattedDate,
      formatCurrency(0),
      formatCurrency(monthlyPayment),
      formatCurrency(monthlyPayment),
    ]);
  }

  // Aplicar o aporte no mês de antecipação
  const dueDateAporte = new Date(startDateObj);
  dueDateAporte.setMonth(startDateObj.getMonth() + anticipateMonth - 1);
  saldoAtual -= entryValue;

  tableData.push([
    anticipateMonth,
    dueDateAporte.toLocaleDateString("pt-BR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    }),
    formatCurrency(entryValue),
    formatCurrency(monthlyPayment),
    formatCurrency(monthlyPayment),
  ]);

  // Recalcular as parcelas restantes após o aporte
  const remainingMonths = months - anticipateMonth;
  const newMonthlyPayment = (saldoAtual / remainingMonths).toFixed(2);

  // Adicionar parcelas restantes
  for (let i = anticipateMonth + 1; i <= months; i++) {
    const dueDate = new Date(dueDateAporte);
    dueDate.setMonth(dueDate.getMonth() + (i - anticipateMonth));
    const formattedDate = dueDate.toLocaleDateString("pt-BR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });

    saldoAtual -= newMonthlyPayment;
    tableData.push([
      i,
      formattedDate,
      formatCurrency(0),
      formatCurrency(newMonthlyPayment),
      formatCurrency(newMonthlyPayment),
    ]);
  }

  console.log("Saldo final após todas as parcelas: ", saldoAtual.toFixed(2));

  window.simulationData = {
    clientName: document.getElementById("client-name").value,
    clientPhone: document.getElementById("client-phone").value,
    clientEmail: document.getElementById("client-email").value,
    amount: amount,
    entryValue: entryValue,
    months: months,
    anticipateMonth: anticipateMonth,
    interestRate: interestRate,
    tableData: tableData,
  };
}

function formatCurrency(value) {
  return parseFloat(value).toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
  });
}



function generatePDFContent() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  const data = window.simulationData;
  if (!data || !data.startDate) {
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
    ["Entrada/Aporte", formatCurrency(entryValue)],
    ["Adesão", formatCurrency(adesaoValue)],
    ["Prazo (Meses)", `${months}`],
    ["Taxa de Juros", `${(interestRate * 100).toFixed(2)}%`],
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