<div style="width: 600px; margin-bottom: 35px;">
    <p>Please use this tool to update species estimates in the Catalogue of Life. Estimates can be added to all CoL higher taxa from kingdom to genus.</p>
    <ul>
    <li><strong>Step 1.</strong> Browse to the appropriate taxon in the tree and click on its name. The name of the taxon and its rank will appear in the form on the right.</li>
    <li><strong>Step 2.</strong> Type the estimate species figure and source of information in the form.</li>
    <li><strong>Step 3.</strong> Click 'Submit estimate' to save the data to the database.</li>
    </ul>
    <p>After adding your estimates, as a final step you can <a href="#" id="copy_to_col_link">submit the estimates to the CoL database</a>. Your estimate figure(s) will appear in the tree of this tool and will be copied to the Catalogue of Life. If you omit this step, your data will be included in the next monthly CoL update.</p>
</div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="estimate_form">
<h4>Step 2. Add estimate figure and source of information</h4>
<p>
    <input id="kingdom" name="kingdom" type="hidden" value="">
</p>

<p>
    <label for="name">Taxon</label>
    <input id="name" name="name" type="text" value="" placeholder="Name" size="25">
</p>

<p>
    <label for="rank">Rank</label>
    <input id="rank" name="rank" type="text" value="" placeholder="Rank" size="25">
</p>

<p>
    <label for="estimate">Species estimate</label>
    <input id="estimate" name="estimate" type="text" value="" placeholder="Estimate" size="25">
    <div class="form_legend">Example of data format: 12500</div>
</p>

<p>
    <label for="source">Source of information</label>
    <textarea id="source" name="source" type="text" value="" placeholder="Source" cols="25" rows="5"></textarea>
    <div class="form_legend">Examples of data format:<br>Smith A. (2014). Personal comment.<br>Smith A. (2008). New classification of plants. In: Kew Bull. 63, 2: 28-30. </div>
</p>

<p>
    <input id="id" name="id" type="hidden" value="">
</p>

<p>
    <h4 class="form_step">Step 3.</h4>
    <button id="submit" type="submit">Submit estimate</button>
</p>
</form>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="copy_to_col">
    <input id="copy" name="copy" type="hidden" value="">
</form>
