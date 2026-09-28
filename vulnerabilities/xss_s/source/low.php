<?php

if( isset( $_POST[ 'btnSign' ] ) ) {
    // Get input and HTML-encode so it is stored and rendered as text, not markup
    $message = htmlspecialchars( trim( $_POST[ 'mtxMessage' ] ), ENT_QUOTES, 'UTF-8' );
    $name    = htmlspecialchars( trim( $_POST[ 'txtName' ] ),    ENT_QUOTES, 'UTF-8' );

    // Prepared statement: input is bound as data, never concatenated into SQL
    $stmt = mysqli_prepare( $GLOBALS["___mysqli_ston"], "INSERT INTO guestbook ( comment, name ) VALUES ( ?, ? )" );
    mysqli_stmt_bind_param( $stmt, "ss", $message, $name );
    mysqli_stmt_execute( $stmt );
    mysqli_stmt_close( $stmt );
}

?>
