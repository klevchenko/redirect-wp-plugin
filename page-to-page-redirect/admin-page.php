<?php

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

?>

<div class="wrap">
    <h1>Redirects</h1>

    <form method="post" action="" class="page_to_page_redirect_form">

        <div>
            <div class="one_select_wrap">
                <label for="from_id"><b>From page </b></label>
                <select class="regular-text select2" name="from_id" id="from_id" required="required">
                    <option value="" selected="selected" disabled="disabled">Search by title</option>
                </select>
            </div>
            <div class="one_select_wrap">
                <label for="to_id"><b>To page </b></label>
                <select class="regular-text select2" name="to_id" id="to_id" required="required">
                    <option value="" selected="selected" disabled="disabled">Search by title</option>
                </select>
            </div>
        </div>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save redirect">
        </p>

    </form>
    
    <table id="redirects_table" style="display: none;">
        <tbody id="redirects">

        </tbody>
        <thead>
        <tr>
            <th>
                From
            </th>
            <th>
                To
            </th>
            <th></th>
        </tr>
        </thead>
    </table>

</div>

