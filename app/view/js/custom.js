function closeEvent(typeEvent){
    $.post( "app/ajax/ajax.php", { map: "closeEvent", typeEvent: typeEvent },
        function( data ) {
    });
}