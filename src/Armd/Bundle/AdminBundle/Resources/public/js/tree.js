
$(function(){
	function createTree(nodeToExpand) {
		var res = [],
			resChild = [],
			childDataAjax,
            select_site = $('#select_site option:selected').val(),
			firstNode;
		$.ajax({
				url: 'sublist?node=0&select_site=' + select_site,
				success: function(jsonResponse, textStatus){
                    for (var ti=0, tl=jsonResponse.data.length; ti < tl; ti++)
                    {
                        firstNode = jsonResponse.data[ti];

                        childDataAjax = $.ajax({
                                url: 'sublist?node='+firstNode.id + '&select_site=' + select_site,
                                async: false,
                                success: function(jsonResponse, textStatus){
                                    return jsonResponse;
                                }
                            });
                        childData = $.parseJSON(childDataAjax.responseText).data;
                        resChild[ti] = [];

                        if (childData) {
                            for (var i=0, l=childData.length; i < l; i++){
                                var e = childData[i];

                                var secNodeLinksObj = {"addUrl" : e.addUrl,"editUrl" : e.editUrl,	"copyUrl" : e.copyUrl,	"deleteUrl" : e.deleteUrl};
                                var secNodeLinks = JSON.stringify(secNodeLinksObj);

                                resChild[ti].push({title: e.text, expand: e.expanded, isFolder: e.expandable, href: secNodeLinks, key:e.id,
                                    icon: false, isLazy:e.expandable });
                            }
                        }

                        var firstNodeLinksObj = {"addUrl" : firstNode.addUrl,"editUrl" : firstNode.editUrl,"copyUrl" : firstNode.copyUrl,"deleteUrl" : firstNode.deleteUrl};
                        var firstNodeLinks = JSON.stringify(firstNodeLinksObj);

                        //console.log(firstNodeLinks);

                        res.push({title: firstNode.text, expand: firstNode.expanded, isFolder: firstNode.expandable, href: firstNodeLinks, key:firstNode.id,
                            icon: false, children:resChild[ti], addClass:'main_folder'});
                    }
						
						
						
					$("#tree").dynatree({
						children: res,
						checkbox: true,
						onCreate: function(node, span){
							bindMenu(node,span);
						},
						onRender: function(node, span){
							bindMenu(node,span);
						},
						onPostInit:function(isReloading, isError) {
							
							if ($.isArray(nodeToExpand)) {
								var i = 0, 
									l = nodeToExpand.length;
								for (i; i < l; i++) {
									var nodeToOpen = $("#tree").dynatree("getTree").getNodeByKey(nodeToExpand[i]);
									if (nodeToOpen) {
										nodeToOpen.visit( function (node) {
										   node.expand(true);
										},true);
									}; 
								}							
							} else if (nodeToExpand) {
								var nodeToOpen = $("#tree").dynatree("getTree").getNodeByKey(nodeToExpand);
								if (nodeToOpen) {
									nodeToOpen.visit( function (node) {
									   node.expand(true);
									},true);
								}; 
							}
						},
						onLazyRead: function(node,span) {
							//console.log(node);
							var nodeKey;
							if (node.data.key.toString().substr(0,1) == '_' ) {
									nodeKey = node.data.key.toString().substr(1);
								} else {
									nodeKey = node.data.key;
								}
							
							
							if (node.data.key != undefined) {
							
							$.ajax({
									url: 'sublist?node='+nodeKey + '&select_site=' + select_site,
									success: function(jsonResponse, textStatus){
										reslazy = [];
										for(var j=0, len=jsonResponse.data.length; j<len; j++){
											var lazy = jsonResponse.data[j];
											
											var lazyNodeLinksObj = {"addUrl" : lazy.addUrl,"editUrl" : lazy.editUrl,	"copyUrl" : lazy.copyUrl,	"deleteUrl" : lazy.deleteUrl};
											var lazyNodeLinks = JSON.stringify(lazyNodeLinksObj);
											reslazy.push({title: lazy.text, expand: lazy.expanded, isFolder: lazy.expandable, href: lazyNodeLinks, isLazy: lazy.expandable, key:lazy.id, icon: false});
											//parsedArr.push(parsedArrOne);
										};
									
										node.setLazyNodeStatus(DTNodeStatus_Ok);
										node.addChild(reslazy);
									}	
								});	
							};
							
						},
						onClick: function(node, event) {
							if (node.getEventTargetType(event) != 'title' && node.getEventTargetType(event) != 'expander'){
								if (event.target.nodeName == 'IMG') {
									var newUrl = $(event.target).parent().attr('href');
									if (event.button==1) {
										window.open(newUrl, '');
									} else {
										window.location.href = newUrl;
									}
									return false;
								}
							} else {
								if (event.target.nodeName == 'A') {
									var newUrl = $(event.target).next('.context-menu').find('a.edit_link').attr('href');
									if (event.button==1) {
										window.open(newUrl, '');
									} else {
										window.location.href = newUrl;
									}
									return false;
								}
							}
						},
						dnd: {
							onDragStart: function(node) {
								//logMsg("tree.onDragStart(%o)", node);
								return true;
							},
							onDragStop: function(node) {
								//logMsg("tree.onDragStop(%o)", node);
								},
								autoExpandMS: 1000,
								preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
								onDragEnter: function(node, sourceNode) {
								//logMsg("tree.onDragEnter(%o, %o)", node, sourceNode);
								return true;
							},
							onDragOver: function(node, sourceNode, hitMode) {
								//logMsg("tree.onDragOver(%o, %o, %o)", node, sourceNode, hitMode);
								// Prevent dropping a parent below it's own child
								if (node.isDescendantOf(sourceNode)){
								  return false;
								}
								// Prohibit creating childs in non-folders (only sorting allowed)
								if ( !node.isFolder && hitMode == "over" )
									return "after";
							},
							onDrop: function(node, sourceNode, hitMode, ui, draggable) {

								var nodeId,
									targetNodeId,
									dropPosition,
									reloadNode,
									parentNode;

								logMsg("tree.onDrop(%o, %o, %s)", node, sourceNode, hitMode);
								//sourceNode.move(node, hitMode);
								// expand the drop target
								//sourceNode.expand(true);

								if (node.data.key.toString().substr(0,1) == '_' ) {
									targetNodeId = node.data.key.toString().substr(1);
								} else {
									targetNodeId = node.data.key;
								};

								if (sourceNode.data.key.toString().substr(0,1) == '_' ) {
									nodeId = sourceNode.data.key.toString().substr(1);
								} else {
									nodeId = sourceNode.data.key;
								};
								reloadNode = sourceNode.getParent().getParent();
								parentNode = node.getParent().getParent();
								if (hitMode == 'over') {
									dropPosition = 'append';
								} else {
									dropPosition = hitMode;
								};

								$.ajax({
									async: true,
									url: 'movePageNode',
									data: {
										nodeId: nodeId,
										targetNodeId: targetNodeId,
										dropPosition: dropPosition
									},
									success: function(jsonResponse) {
										if(!reloadNode.isLazy() || !parentNode.isLazy())  {
											var nodesToOpen;
											if (node.getParent().data.key == sourceNode.getParent().data.key) {
												nodesToOpen = node.getParent().data.key;
											} else {
												nodesToOpen = [node.getParent().data.key, sourceNode.getParent().data.key];
											}	
											$("#tree").dynatree("destroy");										
											createTree(nodesToOpen);
										} else {
											if (reloadNode.isLazy()) {
												reloadNode.reloadChildren();
												reloadNode.expand(true);
											} 
											
											if (parentNode.isLazy() && parentNode != reloadNode) {
												parentNode.reloadChildren();
												parentNode.expand(true);
											} 
										}
									},
									error: function(jqXHR, textStatus, errorThrown) {
										alert('Error');
									}

								});

							}
						}
					});		
				}		
		});		
	};
	createTree();
	
	/**
	 * Контекстное меню для страниц (добавить, копировать, редактировать, удалить)	
	 *
	 */
	function bindMenu(node, span) {
		var linksArr = $.parseJSON($(span).find('a').attr('href'));
		var contectsMenu = $('<span unselectable="on" class="context-menu" >'+
		'<a href="'+linksArr.editUrl+'" class="edit_link"><img alt="" src="/bundles/armdadmin/img/bt-tree-4.png" ></a>'+
		'<a href="'+linksArr.addUrl+'"><img alt="" src="/bundles/armdadmin/img/bt-tree-add.png" ></a>'+
		'<a href="'+linksArr.copyUrl+'"><img alt="" src="/bundles/armdadmin/img/bt-tree-3.png" ></a>'+
		'<a href="'+linksArr.deleteUrl+'"><img alt="" src="/bundles/armdadmin/img/bt-tree-5.png" ></a></span>');
		$(span).append(contectsMenu);
	};
	
	
	
	/**
	 * Actions-меню для дерева (выделить все, удалить, редактировать)
	 *
	 */
	var nt = 0;
	$("#selectAll").click(function(){
		if (nt == 0) {
			$("#tree").dynatree("getRoot").visit(function(node){
				node.select(true);
			});
			nt = 1;
		} else { 
			$("#tree").dynatree("getRoot").visit(function(node){
				node.select(false);
			});
			nt = 0;
		}		
		return false;
    });
	
	$(".ta-delete a").click(function(){
		$("#tree").dynatree("getRoot").visit(function(node){
		if (node.bSelected) {
			$.ajax({
				async: true,
				url: ''+node.data.key+'/delete',
				error: function(jqXHR, textStatus, errorThrown) {
					alert('Error');
				}
			});
			node.remove();
		}
      });
	  $(this).parent().parent('ul').hide();
	  $(this).parents('li.to-open').removeClass('opened');
      return false;
    });
	
	
	$('#treeActions').click(function(){
		$(this).parent().find('ul').slideToggle().end()
						.toggleClass('opened');
	})
	
	
	
 })  

