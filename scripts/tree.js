
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
		    // Load child nodes via ajax GET /getTreeData?mode=children&parent=1234
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
            	var kingdom = getKingdom(node);
            	var rank = node.data.rank;
            	var name = node.data.name;
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
    	$("#estimate_form input:text").each(function() {
    		if ($(this).val().length === 0) {
    			alert(ucFirst($(this).attr('name')) + ' cannot be empty');
    			submitForm = false;
    			return false;
    		}
    	});
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
