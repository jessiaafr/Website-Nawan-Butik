$(document).ready(function () {
  // Handle send button click
  $("#sendBtn").click(sendMessage);

  // Handle enter key press
  $("#userInput").keypress(function (e) {
    if (e.which === 13) {
      sendMessage();
    }
  });

  // Focus input when modal is shown
  $("#chatbotModal").on("shown.bs.modal", function () {
    $("#userInput").focus();
  });
});

function sendMessage() {
  const userInput = $("#userInput");
  const message = userInput.val().trim();

  if (message) {
    // Add user message to chat box
    addMessage(message, "user");
    userInput.val("");

    // Show loading indicator
    const loadingMsg = $('<div class="bot-message">Memproses...</div>');
    $("#chatBox").append(loadingMsg);
    $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);

    // Send to server
    $.post(
      "chatbot.php",
      { message: message },
      function (data) {
        // Remove loading indicator
        loadingMsg.remove();

        // Add bot response
        addMessage(data.text, "bot");

        // If there are products, display them
        if (data.products && data.products.length > 0) {
          let productsHTML = '<div class="row g-3 mt-2">';

          data.products.forEach((product) => {
            productsHTML += `
                        <div class="col-md-6">
                        <a href="${product.product_link}" class="card-link" style="text-decoration: none; color: inherit;">
        <div class="card h-100">
            <img src="${product.full_image_path}" 
                 class="card-img-top product-image" 
                 alt="${product.product_name}"
                 style="height: 200px; object-fit: cover;"
                 onerror="this.onerror=null;this.src='https://via.placeholder.com/300?text=Gambar+Tidak+Tersedia'">
            <div class="card-body">
                <h5 class="card-title">${product.product_name}</h5>
                <p class="card-text text-success fw-bold">${product.formatted_price}</p>
                <p class="card-text">Stok: ${product.stock_status}</p>
                <p class="card-text">Ditambahkan: ${product.formatted_date}</p>
                <p class="card-text">${product.product_description}</p>
            </div>
        </div>
    </div>
                    `;
          });

          productsHTML += "</div>";
          $("#chatBox").append(productsHTML);
        }

        // Scroll to bottom
        $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
      },
      "json"
    ).fail(function () {
      loadingMsg.remove();
      addMessage(
        "Maaf, terjadi kesalahan saat memproses permintaan Anda.",
        "bot"
      );
    });
  }
}

function addMessage(message, sender) {
  const chatBox = $("#chatBox");
  const messageClass = sender === "user" ? "user-message" : "bot-message";

  chatBox.append(`
        <div class="${messageClass}">
            ${message}
        </div>
    `);

  // Scroll to bottom
  chatBox.scrollTop(chatBox[0].scrollHeight);
}
