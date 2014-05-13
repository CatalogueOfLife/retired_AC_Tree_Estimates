<!--
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<p><input id="taxon" name="taxon" type="text" value="<?php echo isset($taxon) ? $taxon : ''; ?>"
    placeholder="Find taxon">
<button id="submit" type="submit">Find</button></p>
</form>
 -->

<p style="width: 600px">Use this form to update species estimates in the Catalogue of Life.
Estimates can be added to all higher taxa (kingdom to genus), but not to (infra)species. Browse to the
appropriate taxon, click its name and add or update the data in the form. When ready, click 'Submit estimate'.</p>

<p style="width: 600px; margin-bottom: 30px;">Submitted estimates are not immediately reflected in the tree, as this would decrease performance.
<a href="">Submit estimates to the CoL database now</a>.</p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="estimate_form">
<p><label for="kingdom">Kingdom</label><input id="kingdom" name="kingdom" type="text" value="" placeholder="Kingdom" size="25"></p>
<p><label for="rank">Rank</label><input id="rank" name="rank" type="text" value="" placeholder="Rank" size="25"></p>
<p><label for="name">Name</label><input id="name" name="name" type="text" value="" placeholder="Name" size="25"></p>
<p><label for="estimate">Estimate</label><input id="estimate" name="estimate" type="text" value="" placeholder="Estimate" size="25"></p>
<p><label for="source">Source</label><textarea id="source" name="source" type="text" value="" placeholder="Source" cols="25" rows="5"></textarea></p>
<p><input id="id" name="id" type="hidden" value=""></p>
<p><button id="submit" type="submit">Submit estimate</button></p>
</form>

