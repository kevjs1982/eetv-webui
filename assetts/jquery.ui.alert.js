$.extend({ alert: function (message, title) {
  $("<div></div>").dialog( {
    buttons: { "Ok": function () { $(this).dialog("close"); } },
    close: function (event, ui) { $(this).remove(); },
    resizable: false,
    title: title,
    modal: true
  }).html(message);
}
});


$.extend({ confirm: function (message, title,callback) {
  $("<div></div>").dialog( {
    buttons: { "Yes" :  function () { $(this).dialog("close");callback(); } , "No": function () { $(this).dialog("close"); }},
	
    close: function (event, ui) { $(this).remove(); },
    resizable: false,
    title: title,
    modal: true
  }).html(message);
}
});