<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 *
 * 탭모듈 관리자 화면을 구성한다.
 * 
 * @file /modules/tab/admin/index.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 21.
 */
if (defined('__IM__') == false) exit;
?>
<script>
Ext.onReady(function () { Ext.getCmp("iModuleAdminPanel").add(
	new Ext.Panel({
		id:"ModuleTab",
		layout:"fit",
		border:false,
		items:[
			new Ext.Panel({
				id:"ModuleTabPanel",
				layout:{type:"hbox",align:"stretch"},
				border:false,
				padding:5,
				items:[
					new Ext.grid.Panel({
						id:"ModuleTabGroupList",
						flex:4,
						border:true,
						disabled:true,
						selected:null,
						title:Tab.getText("admin/group/title"),
						tbar:[
							new Ext.Button({
								text:Tab.getText("admin/group/add"),
								iconCls:"mi mi-plus",
								handler:function() {
									Tab.group.add();
								}
							})
						],
						store:new Ext.data.JsonStore({
							proxy:{
								type:"ajax",
								simpleSortMode:true,
								url:ENV.getProcessUrl("tab","@getGroups"),
								reader:{type:"json"}
							},
							remoteSort:false,
							sorters:[{property:"title",direction:"ASC"}],
							autoLoad:true,
							pageSize:0,
							fields:["idx","title","templet","contexts"],
							listeners:{
								beforeload:function() {
									Ext.getCmp("ModuleTabGroupList").getStore().removeAll();
									Ext.getCmp("ModuleTabGroupList").disable();
									Ext.getCmp("ModuleTabContextList").getStore().removeAll();
									Ext.getCmp("ModuleTabContextList").disable();
								},
								load:function(store,records,success,e) {
									if (success == false) {
										if (e.getError()) {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:e.getError(),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR})
										} else {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_LOAD_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR})
										}
									} else {
										Ext.getCmp("ModuleTabGroupList").enable();
										Ext.getCmp("ModuleTabContextList").disable();
										
										if (Ext.getCmp("ModuleTabGroupList").selected != null) {
											var select = Ext.getCmp("ModuleTabGroupList").getStore().find("idx",Ext.getCmp("ModuleTabGroupList").selected,0,false,false,true);
											if (select > -1 ) Ext.getCmp("ModuleTabGroupList").getSelectionModel().select(select);
											Ext.getCmp("ModuleTabGroupList").selected = null;
										} else {
											Ext.getCmp("ModuleTabContextList").getStore().removeAll();
										}
									}
								}
							}
						}),
						columns:[{
							text:Tab.getText("admin/group/columns/title"),
							width:150,
							dataIndex:"title"
						},{
							text:Tab.getText("admin/group/columns/templet"),
							minWidth:200,
							flex:1,
							dataIndex:"templet"
						},{
							text:Tab.getText("admin/group/columns/contexts"),
							width:80,
							dataIndex:"contexts",
							align:"right",
							renderer:function(value) {
								return Ext.util.Format.number(value,"0,000");
							}
						}],
						selModel:new Ext.selection.RowModel(),
						bbar:[
							new Ext.Button({
								iconCls:"x-tbar-loading",
								handler:function() {
									Ext.getCmp("ModuleTabGroupList").getStore().reload();
								}
							}),
							"->",
							{xtype:"tbtext",text:Admin.getText("text/grid_help")}
						],
						listeners:{
							select:function(grid,record) {
								Ext.getCmp("ModuleTabContextList").getStore().getProxy().setExtraParam("parent",record.data.idx);
								Ext.getCmp("ModuleTabContextList").getStore().reload();
							},
							itemdblclick:function(grid,record) {
								Tab.group.add(record.data.idx);
							},
							itemcontextmenu:function(grid,record,item,index,e) {
								var menu = new Ext.menu.Menu();
			
								menu.add('<div class="x-menu-title">'+record.data.title+'</div>');
								
								menu.add({
									iconCls:"xi xi-form",
									text:"그룹수정",
									handler:function() {
										Tab.group.add(record.data.idx);
									}
								});
								
								menu.add({
									iconCls:"mi mi-trash",
									text:"그룹삭제",
									handler:function() {
										Tab.group.delete(record.data.idx);
									}
								});
								
								e.stopEvent();
								menu.showAt(e.getXY());
							}
						}
					}),
					new Ext.grid.Panel({
						id:"ModuleTabContextList",
						flex:5,
						border:true,
						disabled:true,
						selected:null,
						title:Tab.getText("admin/context/title"),
						tbar:[
							new Ext.Button({
								text:Tab.getText("admin/context/add"),
								iconCls:"mi mi-plus",
								handler:function() {
									Tab.context.add();
								}
							})
						],
						style:{marginLeft:"5px"},
						store:new Ext.data.JsonStore({
							proxy:{
								type:"ajax",
								simpleSortMode:true,
								url:ENV.getProcessUrl("tab","@getContexts"),
								extraParams:{parent:""},
								reader:{type:"json"}
							},
							remoteSort:false,
							sorters:[{property:"sort",direction:"ASC"}],
							autoLoad:false,
							pageSize:0,
							fields:["tab","title","type","context"],
							listeners:{
								load:function(store,records,success,e) {
									Ext.getCmp("ModuleTabContextList").enable();
									
									if (success == false) {
										if (e.getError()) {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:e.getError(),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR})
										} else {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_LOAD_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR})
										}
									} else {
										if (Ext.getCmp("ModuleTabContextList").selected != null) {
											var select = Ext.getCmp("ModuleTabContextList").getStore().find("page",Ext.getCmp("ModuleTabContextList").selected,0,false,false,true);
											if (select > -1 ) Ext.getCmp("ModuleTabContextList").getSelectionModel().select(select);
											Ext.getCmp("ModuleTabContextList").selected = null;
										}
									}
								}
							}
						}),
						columns:[{
							text:Tab.getText("admin/context/columns/tab"),
							width:120,
							dataIndex:"tab"
						},{
							text:Tab.getText("admin/context/columns/title"),
							minWidth:150,
							flex:1,
							dataIndex:"title"
						},{
							text:Admin.getText("configs/sitemap/columns/type"),
							width:80,
							dataIndex:"type",
							renderer:function(value) {
								return Admin.getText("configs/sitemap/type/"+value);
							}
						},{
							text:Admin.getText("configs/sitemap/columns/context"),
							width:200,
							dataIndex:"context"
						}],
						selModel:new Ext.selection.RowModel(),
						bbar:[
							new Ext.Button({
								iconCls:"fa fa-caret-up",
								handler:function() {
									Admin.gridSort(Ext.getCmp("ModuleTabContextList"),"sort","up");
									Admin.gridSave(Ext.getCmp("ModuleTabContextList"),ENV.getProcessUrl("tab","@saveContextSort"),500);
								}
							}),
							new Ext.Button({
								iconCls:"fa fa-caret-down",
								handler:function() {
									Admin.gridSort(Ext.getCmp("ModuleTabContextList"),"sort","down");
									Admin.gridSave(Ext.getCmp("ModuleTabContextList"),ENV.getProcessUrl("tab","@saveContextSort"),500);
								}
							}),
							"-",
							new Ext.Button({
								iconCls:"x-tbar-loading",
								handler:function() {
									Ext.getCmp("ModuleTabContextList").getStore().reload();
								}
							}),
							"->",
							{xtype:"tbtext",text:Admin.getText("text/grid_help")}
						],
						listeners:{
							itemdblclick:function(grid,record) {
								Tab.context.add(record.data.tab);
							},
							itemcontextmenu:function(grid,record,item,index,e) {
								var menu = new Ext.menu.Menu();
			
								menu.add('<div class="x-menu-title">'+record.data.title+'</div>');
								
								menu.add({
									iconCls:"xi xi-form",
									text:"컨텍스트 수정",
									handler:function() {
										Tab.context.add(record.data.tab);
									}
								});
								
								menu.add({
									iconCls:"mi mi-trash",
									text:"컨텍스트 삭제",
									handler:function() {
										Tab.context.delete(record.data.parent,record.data.tab);
									}
								});
								
								e.stopEvent();
								menu.showAt(e.getXY());
							}
						}
					})
				]
			})
		]
	})
); });
</script>