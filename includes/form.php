<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

<p>
<input id="taxon" name="taxon" type="text" value="<?php echo isset($taxon) ? $taxon : ''; ?>"
    placeholder="Find taxon">
<button id="submit" type="submit">Find</button>
</p>

</form>
