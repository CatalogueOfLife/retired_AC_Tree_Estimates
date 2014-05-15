var higherTaxa = ["class", "family", "kingdom", "not assigned", "order",
   "phylum", "superfamily", "genus"];

function getKingdom (node) {
    if (node.parent.title == 'root')
        return node.data.name;
    else
    	return getKingdom(node.parent);
}

$(function() {
    // Create the tree inside the <div id="tree"> element.
    $("#tree").fancytree({
        source: treeSource,
		lazyLoad: function(event, data){
		    var node = data.node;
		    data.result = {
	    	    url: "ajax/tree.php?id=",
	    	    data: {
		    	    id: node.key
		    	},
	    	    cache: false
    	    };
	    },
	    icons: false,
	    click: function(event, data) {
            var node = data.node;
            // Only for click and dblclick events:
            // 'title' | 'prefix' | 'expander' | 'checkbox' | 'icon'
            if (data.targetType == 'title') {
            	if ($.inArray(node.data.rank, higherTaxa) === -1) {
            		alert('Only higher taxa can have an estimate');
            		return false;
            	}
            	$.ajax({
            		url: "ajax/get_estimate.php",
            		data: {
          			  name: node.data.name,
          			  rank: node.data.rank,
          			  kingdom: getKingdom(node)
            		},
            		dataType: "json"
            	}).done(getEstimateForm);
            }
	    }
    });
});

function getEstimateForm(data) {
	$("#estimate_form input,textarea").each(function() {
		for(var k in data) {
			if ($(this).attr('id') == k) {
				$(this).val(data[k]);
			}
		}
	});
}

function ucFirst(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
}

$(function() {
    $("#estimate_form").submit(function() {
    	var submitForm = true;
    	// Text fields cannot be empty
    	$("#estimate_form input:text").each(function() {
    		if ($(this).val().length === 0) {
    			alert(ucFirst($(this).attr('name')) + ' cannot be empty');
    			submitForm = false;
    			return false;
    		}
    	});
    	if (isNaN($('#estimate').val())) {
    		alert('Estimate ' + $('#estimate').val() + ' is not a number');
    		submitForm = false;
    		return false;
    	}
    	if (submitForm) {
        	$.ajax({
        		type: "post",
        		url: "ajax/save_estimate.php",
        		data: $("#estimate_form").serialize(),
        		success: function(data) {
        			alert(data);
        		}
        	});
        	$("#estimate_form")[0].reset();
    	}
    	return false;
    });
});

$(function() {
	$("#alert").delay(2000).fadeOut(500);
});

$(function() {
	$("#copy_to_col_link").click(function() {
		$("#copy_to_col").submit();
		return false;
	})
});