$.each({
    jstreePlugin: function (field, other_field, options) {
        
        options['core']['data'].data = function(node){return {'id': node.id};};
        //$.univ().dump(options['core'],'console',10);
//        alert(field);
        $(field).jstree(options)
                .delegate("a", "click", function (e) {
                        $(other_field).val($(field).jstree('get_selected'));
                        $(other_field).submit();
                    }
//                    else {
//                        // $.univ.dump(data,"console");
//                        $(other_field).val($(field).jstree('get_selected').attr('id'));
//                        $(field).jstree("open_node", this);
////                $(other_field).submit();
//                    }
                );
    },
    jstreeRefresh: function (field){
          $(field).jstree('refresh');
    }
}, $.univ._import);


//    var options={ 
//		// List of active plugins
//		"plugins" : [ 
//			"themes","html_data","ui","crrm","cookies","dnd","search","types","hotkeys"
//		],
//		// Using types - most of the time this is an overkill
//		// read the docs carefully to decide whether you need types
//		"types" : {
//			// I set both options to -2, as I do not need depth and children count checking
//			// Those two checks may slow jstree a lot, so use only when needed
//			"max_depth" : -2,
//			"max_children" : -2,
//			// I want only `drive` nodes to be root nodes 
//			// This will prevent moving or creating any other type as a root node
//			"valid_children" : [ "drive" ],
//			"types" : {
//				// The default type
//				"default" : {
//					// I want this type to have no children (so only leaf nodes)
//					// In my case - those are files
//					"valid_children" : "none",
//					// If we specify an icon for the default type it WILL OVERRIDE the theme icons
//					"icon" : {
//						"image" : "./wr-addons/jsTree/js/themes/default/gray-broadcast-tower-icon_sml.png"
//					}
//				},
//				// The `folder` type
//				"region" : {
//					// can have files and other folders inside of it, but NOT `drive` nodes
//					"valid_children" : [ "default", "folder" ],
//					"icon" : {
//						"image" : "./wr-addons/jsTree/js/themes/default/folder.png"
//					}
//				},
//				// The `drive` nodes 
//				"project" : {
//					// can have files and folders inside, but NOT other `drive` nodes
//					"valid_children" : [ "default", "folder" ],
//					"icon" : {
//						"image" : "./wr-addons/jsTree/js/themes/default/root.png"
//					},
//					// those prevent the functions with the same name to be used on `drive` nodes
//					// internally the `before` event is used
//					"start_drag" : false,
//					"move_node" : false,
//					"delete_node" : false,
//					"remove" : false
//				}
//			}
//		},
//		// UI & core - the nodes to initially select and open will be overwritten by the cookie plugin
//
//		// the UI plugin - it handles selecting/deselecting/hovering nodes
//		"ui" : {
//			// this makes the node with ID node_4 selected onload
//			"initially_select" : [ "node_4" ]
//		},
//		// the core plugin - not many options here
//		"core" : { 
//			// just open those two nodes up
//			// as this is an AJAX enabled tree, both will be downloaded from the server
//			"initially_open" : [ "node_2" , "node_3" ] 
//		}
//	};
//    this.jstree(options);
////    this.jstree(options).bind("select_node.jstree", function (event, data) {
//////            alert(data.rslt.obj.attr("id"));
////        });