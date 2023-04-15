// Add Batch //
document.addEventListener("DOMContentLoaded", function () {
    const batchForm = document.getElementById("add-batch-form");
  
    batchForm.addEventListener("submit", function (event) {
      event.preventDefault(); // Förhindra formulärets standardbeteende
  
      const formData = new FormData(batchForm);
  
      fetch("add_batch.php", {
        method: "POST",
        body: new URLSearchParams(formData).toString(),
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
      })
        .then((response) => response.json())
        .then((data) => {
          const messageElement = document.getElementById("message");
          messageElement.innerText = data.message;
        })
        .catch((error) => {
          console.error("Error:", error);
        });
    });
  });

  $(document).ready(function() {
    $(".add-batch-btn").on("click", function() {
      const produktId = $(this).data("produkt-id");
      $("#produkt_id").val(produktId);
      $("#batch-select").empty(); // Töm select-elementet
      $.ajax({
        url: "get-batches.php?produkt_id=" + produktId,
        method: "POST",
        data: { produkt_id: produktId },
        dataType: "json",
        success: function(response) {
          // Lägg till batcherna i select-elementet
          for (let i = 0; i < response.length; i++) {
            const { batch_nummer, utgangsdatum } = response[i];
            $("#batch-select").append(`<option value='${batch_nummer}'>${batch_nummer} - ${utgangsdatum}</option>`);
          }
          $(".modal").show();
        },
        error: function(xhr, status, error) {
          console.log("Ajax error:", error);
        }
      });
    });
  
    $(".close").on("click", function() {
      $(".modal").hide();
    });
  
  
  
    function addBatch(produktId, batchNummer, utgangsdatum) {
      $.ajax({
        url: "add_batch.php",
        method: "POST",
        data: {
          produkt_id: produktId,
          batch_nummer: batchNummer,
          utgangsdatum: utgangsdatum,
        },
        success: function (response) {
          console.log("Ajax success:", response);
          const parsedResponse = JSON.parse(response);
          if (parsedResponse.success) {
            // Stäng modal-fönstret
            $(".modal").hide();
            // Hitta den specifika raden i tabellen med hjälp av produkt-ID:t
            const tableRow = $(`tr[data-produkt-id='${produktId}']`);
            // Uppdatera batch-select i den hittade raden
            const batchSelect = tableRow.find("#batch-select");
            batchSelect.append(
              `<option value='${batchNummer}'>${batchNummer} - ${utgangsdatum}</option>`
            );
          } else {
            // Visa felmeddelande
            console.log("Kunde inte lägga till batchen");
          }
        },
        error: function (xhr, status, error) {
          console.log("Ajax error:", error);
        },
      });
    }
    
  
  
  
    $("#delete-batch-btn").on("click", function() {
      const batchId = $("#batch_nummer").val();
      const produktId = $("#produkt_id").val();
      // ...
    });
  });
  

  /*Start Sortering Cod */

 $(document).ready(function() {
    console.log("Document is ready");

    function sortTable(table, columnIndex, asc = true) {
        console.log("sortTable called");
        const tbody = table.find("tbody");
        const rows = tbody.find("tr").toArray();

        rows.sort(function(a, b) {
            const cellA = $(a).children("td").eq(columnIndex);
            const cellB = $(b).children("td").eq(columnIndex);
            const valueA = cellA.text();
            const valueB = cellB.text();
            if ($.isNumeric(valueA) && $.isNumeric(valueB)) {
                return asc ? valueA - valueB : valueB - valueA;
            } else {
                return asc ? valueA.localeCompare(valueB) : valueB.localeCompare(valueA);
            }
        });

        tbody.append(rows);
    }

    $(document).on("click", ".sortable", function() {
        console.log("Column header clicked");
        const columnIndex = $(this).index();
        const table = $(this).closest("table");
        const asc = !$(this).hasClass("asc");
        $(".sortable").removeClass("asc desc");
        $(this).toggleClass("asc", asc);
        $(this).toggleClass("desc", !asc);
        sortTable(table, columnIndex, asc);
    });
});


  /*End Sortering kod */

  /*Start Sök knapp */
  $(document).ready(function () {
    function filterTable(searchText) {
      $("table tbody tr").each(function () {
        const rowText = $(this).find('td:not(:last)').text().toLowerCase(); // Ignorerar den sista cellen i raden (knapparna)
        if (rowText.includes(searchText)) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
    }
  
    // Sökfilter för tabellen
    $("#search").on("input", function () {
      const searchText = $(this).val().toLowerCase();
      filterTable(searchText);
    });
  });
  
  $("#search").on("input", function () {
    const searchText = $(this).val().toLowerCase();
    console.log("Filtering with search text:", searchText);
    filterTable(searchText);
  });
  
  
  /*End Sök Snapp*/


  




/*Edit knapp */
$(document).ready(function () {
  $('.edit-produkt-btn').on('click', function () {
    const productId = $(this).data('produkt-id');
    const batchNummer = $(this).data('batch-nummer');
    const utgangsdatum = $(this).data('utgangsdatum');

    editBatch(productId, batchNummer, utgangsdatum);
  });

  function editBatch(productId, batchNummer, utgangsdatum) {
    // Hämta vald batch från rätt rullgardinsmeny
    const selectedBatchNummer = $(`#batch-select-${productId}`).val();

    // Hämta batch-information för det valda batch-numret
    const batchInfoQuery = `SELECT utgangsdatum, antal FROM batcher WHERE produkt_id = ${productId} AND batch_nummer = '${selectedBatchNummer}'`;

    console.log("batchInfoQuery: ", batchInfoQuery);

    $.get("get-data.php", { query: batchInfoQuery }, function (data) {
      if (data && data.length > 0) {
        const selectedBatchInfo = data[0];

        // Visa redigeringsmodalen
        $('.modal-edit').show();

        // Fyll i formuläret med valda värden
        $('#edit-produkt-id').val(productId);
        $('#edit-batch-nummer').val(selectedBatchNummer);
        $('#edit-utgangsdatum').val(selectedBatchInfo.utgangsdatum);
        $('#edit-antal').val(selectedBatchInfo.antal);
      } else {
        console.error('Ingen data returnerades från servern för batchinformation');
      }
    }).fail(function (xhr, status, error) {
      console.error('AJAX-fel:', status, error);
    });
  }

  function saveBatch() {
    const productId = $('#edit-produkt-id').val();
    const batchNummer = $('#edit-batch-nummer').val();
    const Antal = $('#edit-antal').val();
    const Utgangsdatum = $('#edit-utgangsdatum').val();
  
    $.post('edit_batch.php', {
      product_id: productId,
      batch_nummer: batchNummer,
      antal: Antal,
      utgangsdatum: Utgangsdatum
    }).done(function (data) {
      // Anropet lyckades, kontrollera svarets innehåll manuellt
      if (data && data.message && data.success) {
        console.log(data.message); // Logga serverns svar för felsökning
        // Uppdatera batch-informationen på sidan efter att ändringarna har sparats
        updateBatchInfo(productId, batchNummer, Utgangsdatum, Antal);
        $('.modal-edit').hide();
      } else {
        console.error("Svaret från servern var inte giltigt.");
      }
    }).fail(function (xhr, status, error) {
      console.error('AJAX-fel:', status, error); // Logga eventuella AJAX-fel
    });
  }






$(document).ready(function () {
  // ...

  // Stäng redigeringsmodalen när användaren klickar på krysset
  $('.close-edit').on('click', function () {
      $('.modal-edit').hide();
  });

  // Stäng redigeringsmodalen när användaren klickar utanför modalen
  $(window).on('click', function (event) {
      if ($(event.target).hasClass('modal-edit')) {
          $('.modal-edit').hide();
      }
  });

 

  // Lägg till en händelselyssnare för att hantera formulärinskickning
  $('#edit-batch-form').on('submit', function (event) {
    // Förhindra att sidan laddas om vid inskickning
    event.preventDefault();

    // Spara ändringar
    saveBatch();
  });
});
});