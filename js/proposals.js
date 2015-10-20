
// search - filter table by textbox
jQuery.fn.filterByText = function(textbox) {
  return this.each(function() {
    var table = this;
    var rows = [];

    // get all rows in table area (e.g. tbody)
    $(table).find('tr').each(function(){
      var rowCopy = $(this).clone();
      var rowName = rowCopy.find('td').get(0).innerHTML;
      rows.push({element: rowCopy, text: rowName}); // copy all row data
    });

    $(table).data('rows', rows);


    $(textbox).bind('change keyup', function() {
      var rows = $(table).empty().scrollTop(0).data('rows');

      var search = $.trim($(this).val());
      var regex = new RegExp(search,'gi');

      $.each(rows, function(i) {
        var row = rows[i];
        var isMatch = (row.text.match(regex) !== null);

        if(isMatch) {
          $(table).append(
            $(row.element)
          );
        }
      });
    });
  });
};

function updateRowTotal(row, callback)
{
  // get values
  var priceValue = row.find('.price').get(0).innerHTML;   // get price
  var countValue = row.find('.count').get(0).value;       // get count value
  var discountValue = row.find('.discount').get(0).value; // get discount value

  // calculate total
  var total = 0;
  var priceNumber = parseFloat(priceValue);
  var isValidPrice = !isNaN(priceNumber);

  if(isValidPrice)
  {
    var discountNumber = parseInt(discountValue);
    if(isNaN(discountNumber))
    {
      discountNumber = 0;
    }
    else
    {
      discountNumber = Math.abs(discountNumber);

      if(discountNumber > 100)
      {
        discountNumber = 100;
      }
    }

    var countNumber = parseInt(countValue);
    if(isNaN(countNumber))
    {
      countNumber = 0;
    }
    countNumber = Math.abs(countNumber);

    total = ((priceNumber * countNumber) * (100 - discountNumber)) / 100;
  }

  // update total cell in same row
  row.find('.total').get(0).innerHTML = parseFloat(total).toFixed(2);

  // run callback fn
  callback();
}

function updateLineItemTotal()
{
  var total = 0;
  $(".line-item-list tbody tr .total").each(function(){
    var rowTotal = $(this).get(0).innerHTML;

    var rowTotalNumber = parseFloat(rowTotal);
    var isValidTotal = !isNaN(rowTotalNumber);
    if(isValidTotal)
    {
      total = total + rowTotalNumber;
    }
  });

  total = parseFloat(total).toFixed(2);

  $(".list-total-area .list-total").get(0).innerHTML = total;
}

$(function() {
  $('.customer-list tbody').filterByText($('.customer-search .search-box'));  // add search functionality to search box
  $('.line-item-list tbody tr').each(function(){
    var row = $(this);

  });

  $('.line-item-list tr td .count, .line-item-list tr td .discount').on('change keyup', function(){
    var row = $(this).parent().parent();
    updateRowTotal(row, updateLineItemTotal);
  });
});  
