/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodule.kr)
 *
 * 탭모듈 관리자 UI이벤트를 처리한다.
 * 
 * @file /modules/tab/admin/scripts/script.js
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 7. 14.
 */
var Tab = {
	/**
	 * 탭 그룹관리
	 */
	group:{
		/**
		 * 탭 그룹추가
		 *
		 * @param int idx 탭 그룹 고유값 (없을경우 추가)
		 */
		add:function(idx) {
			new Ext.Window({
				id:"ModuleTabGroupAddWindow",
				title:(idx ? Tab.getText("admin/group/modify") : Tab.getText("admin/group/add")),
				width:600,
				modal:true,
				autoScroll:true,
				border:false,
				items:[
					new Ext.form.Panel({
						id:"ModuleTabGroupAddForm",
						border:false,
						bodyPadding:"10 10 5 10",
						fieldDefaults:{labelAlign:"right",labelWidth:100,anchor:"100%",allowBlank:false},
						items:[
							new Ext.form.Hidden({
								name:"idx"
							}),
							new Ext.form.TextField({
								fieldLabel:Tab.getText("admin/group/form/title"),
								name:"title",
								afterBodyEl:'<div class="x-form-help">'+Tab.getText("admin/group/form/title_help")+'</div>'
							}),
							Admin.templetField(Tab.getText("admin/group/form/templet"),"templet","module","tab",false)
						]
					})
				],
				buttons:[
					new Ext.Button({
						text:Admin.getText("button/confirm"),
						handler:function() {
							Ext.getCmp("ModuleTabGroupAddForm").getForm().submit({
								url:ENV.getProcessUrl("tab","@saveGroup"),
								submitEmptyText:false,
								waitTitle:Admin.getText("action/wait"),
								waitMsg:Admin.getText("action/saving"),
								success:function(form,action) {
									Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/saved"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
										
										Ext.getCmp("ModuleTabGroupList").getStore().reload();
										Ext.getCmp("ModuleTabGroupAddWindow").close();
									}});
								},
								failure:function(form,action) {
									if (action.result) {
										if (action.result.message) {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
										} else {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_SAVE_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
										}
									} else {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("INVALID_FORM_DATA"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									}
								}
							});
						}
					}),
					new Ext.Button({
						text:Admin.getText("button/cancel"),
						handler:function() {
							Ext.getCmp("ModuleTabGroupAddWindow").close();
						}
					})
				],
				listeners:{
					show:function() {
						if (idx) {
							Ext.getCmp("ModuleTabGroupAddForm").getForm().load({
								url:ENV.getProcessUrl("tab","@getGroup"),
								params:{idx:idx},
								waitTitle:Admin.getText("action/wait"),
								waitMsg:Admin.getText("action/loading"),
								success:function(form,action) {
									
								},
								failure:function(form,action) {
									if (action.result && action.result.message) {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									} else {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_LOAD_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									}
									Ext.getCmp("ModuleTabGroupAddWindow").close();
								}
							});
						}
					}
				}
			}).show();
		},
		delete:function(idx) {
			Ext.Msg.show({title:Admin.getText("alert/info"),msg:"선택한 탭 그룹을 삭제하시겠습니까?<br>탭 그룹을 삭제할 경우 하위에 탭 컨텍스트도 함께 삭제됩니다.",buttons:Ext.Msg.OKCANCEL,icon:Ext.Msg.QUESTION,fn:function(button) {
				if (button == "ok") {
					Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/wait"));
					$.send(ENV.getProcessUrl("tab","@deleteGroup"),{idx:idx},function(result) {
						if (result.success == true) {
							Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/worked"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
								Ext.getCmp("ModuleTabGroupList").getStore().reload();
							}});
						}
					});
				}
			}});
		}
	},
	/**
	 * 컨텍스트관리
	 */
	context:{
		/**
		 * 컨텍스트추가
		 *
		 * @param string tab 탭 고유값 (없을 경우 추가)
		 */
		add:function(tab) {
			var parent = Ext.getCmp("ModuleTabContextList").getStore().getProxy().extraParams.parent;
			
			new Ext.Window({
				id:"ModuleTabContextAddWindow",
				title:(tab ? Tab.getText("admin/context/modify") : Tab.getText("admin/context/add")),
				width:700,
				modal:true,
				border:false,
				resizeable:false,
				autoScroll:true,
				items:[
					new Ext.form.Panel({
						id:"ModuleTabContextAddForm",
						border:false,
						bodyPadding:"10 10 5 10",
						fieldDefaults:{labelAlign:"right",labelWidth:100,anchor:"100%",allowBlank:false},
						items:[
							new Ext.form.Hidden({
								name:"parent",
								value:parent
							}),
							new Ext.form.Hidden({
								name:"oTab",
								value:(tab ? tab : "")
							}),
							new Ext.form.FieldSet({
								title:Tab.getText("admin/context/form/default_setting"),
								items:[
									new Ext.form.TextField({
										fieldLabel:Tab.getText("admin/context/form/tab"),
										name:"tab",
										afterBodyEl:'<div class="x-form-help">'+Tab.getText("admin/context/form/tab_help")+'</div>'
									}),
									new Ext.form.TextField({
										fieldLabel:Tab.getText("admin/context/form/title"),
										name:"title"
									}),
									new Ext.form.TextField({
										fieldLabel:Tab.getText("admin/context/form/description"),
										name:"description",
										allowBlank:true
									}),
									new Ext.form.ComboBox({
										fieldLabel:Admin.getText("configs/sitemap/form/type"),
										name:"type",
										store:new Ext.data.ArrayStore({
											fields:["display","value"],
											data:(function() {
												var datas = [];
												for (var type in Admin.getText("configs/sitemap/type")) {
													if (type == "PAGE") continue;
													datas.push([Admin.getText("configs/sitemap/type/"+type),type]);
												}
												
												return datas;
											})()
										}),
										displayField:"display",
										valueField:"value",
										emptyText:Admin.getText("configs/sitemap/form/type_help"),
										listeners:{
											change:function(form,value) {
												Ext.getCmp("ModuleTabContextAddMODULE").hide().disable();
												Ext.getCmp("ModuleTabContextAddEXTERNAL").hide().disable();
												Ext.getCmp("ModuleTabContextAddWIDGET").hide().disable();
												Ext.getCmp("ModuleTabContextAddLINK").hide().disable();
												
												if (value != "EMPTY" && value != "HTML") Ext.getCmp("ModuleTabContextAdd"+value).show().enable();
											}
										}
									})
								]
							}),
							new Ext.form.FieldSet({
								id:"ModuleTabContextAddMODULE",
								title:Admin.getText("configs/sitemap/form/context"),
								items:[
									new Ext.form.FieldContainer({
										fieldLabel:Admin.getText("configs/sitemap/form/module"),
										layout:"hbox",
										items:[
											new Ext.form.ComboBox({
												name:"target",
												store:new Ext.data.JsonStore({
													proxy:{
														type:"ajax",
														url:ENV.getProcessUrl("tab","@getContextModules"),
														reader:{type:"json"}
													},
													autoLoad:true,
													remoteSort:false,
													sorters:[{property:"module",direction:"ASC"}],
													fields:["module","title"]
												}),
												displayField:"title",
												valueField:"module",
												flex:1,
												listeners:{
													change:function(form,value) {
														Ext.getCmp("ModuleTabContextAddForm").getForm().findField("context").getStore().getProxy().setExtraParam("target",value);
														Ext.getCmp("ModuleTabContextAddForm").getForm().findField("context").getStore().load();
													}
												}
											})
										]
									}),
									new Ext.form.FieldContainer({
										fieldLabel:Admin.getText("configs/sitemap/form/context"),
										layout:"hbox",
										items:[
											new Ext.form.ComboBox({
												name:"context",
												disabled:true,
												_configs:{},
												store:new Ext.data.JsonStore({
													proxy:{
														type:"ajax",
														simpleSortMode:true,
														url:ENV.getProcessUrl("admin","@getModuleContexts"),
														extraParams:{target:""},
														reader:{type:"json"}
													},
													remoteSort:false,
													sorters:[{property:"module",direction:"ASC"}],
													fields:["context","title"],
													listeners:{
														load:function(store,records,success,e) {
															Ext.getCmp("ModuleTabContextAddForm").getForm().findField("context").reset();
															
															if (success == true) {
																Ext.getCmp("ModuleTabContextAddForm").getForm().findField("context").enable();
															} else {
																if (e.getError()) {
																	Ext.Msg.show({title:Admin.getText("alert/error"),msg:e.getError(),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
																} else {
																	Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_LOAD_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
																}
																Ext.getCmp("ModuleTabContextAddForm").getForm().findField("target").reset();
																Ext.getCmp("ModuleTabContextAddForm").getForm().findField("context").disable();
															}
														}
													}
												}),
												displayField:"title",
												valueField:"context",
												flex:1,
												listeners:{
													change:function(form,value) {
														Ext.getCmp("ModuleTabContextAddContextConfigs").hide();
														Ext.getCmp("ModuleTabContextAddContextConfigs").removeAll();
														
														if (value) {
															$.ajax({
																type:"POST",
																url:ENV.getProcessUrl("tab","@getModuleContextConfigs"),
																data:{parent:parent,tab:form.getForm().findField("tab").getValue(),target:form.getStore().getProxy().extraParams.target,context:value},
																dataType:"json",
																success:function(result) {
																	if (result.success == true) {
																		Ext.getCmp("ModuleTabContextAddContextConfigs").hide();
																		Ext.getCmp("ModuleTabContextAddContextConfigs").removeAll();
																		
																		for (var i=0, loop=result.configs.length;i<loop;i++) {
																			if (result.configs[i].type == "templet") {
																				Ext.getCmp("ModuleTabContextAddContextConfigs").add(Admin.templetField(result.configs[i].title,"@"+result.configs[i].name,"module",result.configs[i].target,result.configs[i].use_default,ENV.getProcessUrl("tab","@getTempletConfigs"),{parent:parent,tab:form.getForm().findField("tab").getValue(),module:form.getForm().findField("target").getValue()}));
																				form.getForm().findField("@"+result.configs[i].name).setValue(result.configs[i].value);
																			}
																			
																			if (result.configs[i].type == "select") {
																				Ext.getCmp("ModuleTabContextAddContextConfigs").add(
																					new Ext.form.ComboBox({
																						fieldLabel:result.configs[i].title,
																						name:"@"+result.configs[i].name,
																						store:new Ext.data.ArrayStore({
																							fields:["value","display"],
																							data:result.configs[i].data
																						}),
																						displayField:"display",
																						valueField:"value",
																						value:form._configs[result.configs[i].name] ? form._configs[result.configs[i].name] : result.configs[i].value
																					})
																				);
																			}
																		}
																		
																		if (Ext.getCmp("ModuleTabContextAddContextConfigs").items.length > 0) {
																			Ext.getCmp("ModuleTabContextAddContextConfigs").show();
																		}
																	}
																}
															});
														}
													}
												}
											})
										]
									}),
									new Ext.form.FieldContainer({
										id:"ModuleTabContextAddContextConfigs",
										layout:{type:"vbox",align:"stretch"},
										style:{marginBottom:"0px"},
										items:[]
									})
								],
								listeners:{
									afterlayout:function() {
										Ext.getCmp("ModuleTabContextAddWindow").center();
									}
								}
							}),
							new Ext.form.FieldSet({
								id:"ModuleTabContextAddEXTERNAL",
								title:Admin.getText("configs/sitemap/form/context"),
								items:[
									new Ext.form.ComboBox({
										fieldLabel:Admin.getText("configs/sitemap/form/external"),
										name:"external",
										store:new Ext.data.JsonStore({
											proxy:{
												type:"ajax",
												url:ENV.getProcessUrl("admin","@getExternals"),
												reader:{type:"json"}
											},
											autoLoad:true,
											remoteSort:false,
											sorters:[{property:"path",direction:"ASC"}],
											fields:["path"]
										}),
										displayField:"path",
										valueField:"path",
										afterBodyEl:'<div class="x-form-help">'+Admin.getText("configs/sitemap/form/external_help")+'</div>'
									})
								]
							}),
							new Ext.form.FieldSet({
								id:"ModuleTabContextAddWIDGET",
								title:Admin.getText("configs/sitemap/form/context"),
								items:[
									new Ext.form.TextArea({
										fieldLabel:Admin.getText("configs/sitemap/form/widget"),
										name:"widget",
										value:"[]",
										afterBodyEl:'<div class="x-form-help">'+Admin.getText("configs/sitemap/form/widget_help")+'</div>'
									})
								],
								listeners:{
									afterlayout:function() {
										Ext.getCmp("ModuleTabContextAddWindow").center();
									}
								}
							}),
							new Ext.form.FieldSet({
								id:"ModuleTabContextAddLINK",
								title:Admin.getText("configs/sitemap/form/context"),
								items:[
									new Ext.form.FieldContainer({
										fieldLabel:Admin.getText("configs/sitemap/form/link"),
										layout:"hbox",
										items:[
											new Ext.form.TextField({
												name:"link_url",
												flex:1,
												style:{marginRight:"5px"}
											}),
											new Ext.form.ComboBox({
												name:"link_target",
												store:new Ext.data.ArrayStore({
													fields:["display","value"],
													data:[[Admin.getText("configs/sitemap/form/link_target")._self,"_self"],[Admin.getText("configs/sitemap/form/link_target")._blank,"_blank"]]
												}),
												displayField:"display",
												valueField:"value",
												value:"_self",
												width:120
											})
										]
									})
								],
								listeners:{
									afterlayout:function() {
										Ext.getCmp("ModuleTabContextAddWindow").center();
									}
								}
							})
						]
					})
				],
				buttons:[
					new Ext.Button({
						text:Admin.getText("button/confirm"),
						handler:function() {
							Ext.getCmp("ModuleTabContextAddForm").getForm().submit({
								url:ENV.getProcessUrl("tab","@saveContext"),
								submitEmptyText:false,
								waitTitle:Admin.getText("action/wait"),
								waitMsg:Admin.getText("action/saving"),
								success:function(form,action) {
									Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/saved"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function(button) {
										Ext.getCmp("ModuleTabGroupList").selected = parent;
										Ext.getCmp("ModuleTabGroupList").getStore().reload();
										Ext.getCmp("ModuleTabContextAddWindow").close();
									}});
								},
								failure:function(form,action) {
									if (action.result) {
										if (action.result.message) {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
										} else {
											Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_SAVE_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
										}
									} else {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("INVALID_FORM_DATA"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									}
								}
							});
						}
					}),
					new Ext.Button({
						text:Admin.getText("button/cancel"),
						handler:function() {
							Ext.getCmp("ModuleTabContextAddWindow").close();
						}
					})
				],
				listeners:{
					show:function() {
						if (tab) {
							Ext.getCmp("ModuleTabContextAddForm").getForm().load({
								url:ENV.getProcessUrl("tab","@getContext"),
								params:{parent:parent,tab:tab},
								waitTitle:Admin.getText("action/wait"),
								waitMsg:Admin.getText("action/loading"),
								success:function(form,action) {
									form.findField("context")._configs = action.result.data._configs ? action.result.data._configs : {};
									
									if (action.result.data.type == "MODULE") {
										form.findField("context").getStore().getProxy().setExtraParam("target",action.result.data.target);
										form.findField("context").getStore().load(function() {
											form.findField("context").setValue(action.result.data._context);
										});
									}
									
									Ext.getCmp("ModuleTabContextAddWindow").center();
								},
								failure:function(form,action) {
									if (action.result && action.result.message) {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:action.result.message,buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									} else {
										Ext.Msg.show({title:Admin.getText("alert/error"),msg:Admin.getErrorText("DATA_LOAD_FAILED"),buttons:Ext.Msg.OK,icon:Ext.Msg.ERROR});
									}
									Ext.getCmp("ModuleTabContextAddWindow").close();
								}
							});
						} else {
							Ext.getCmp("ModuleTabContextAddMODULE").hide().disable();
							Ext.getCmp("ModuleTabContextAddEXTERNAL").hide().disable();
							Ext.getCmp("ModuleTabContextAddWIDGET").hide().disable();
							Ext.getCmp("ModuleTabContextAddLINK").hide().disable();
						}
					}
				}
			}).show();
		},
		delete:function(parent,tab) {
			Ext.Msg.show({title:Admin.getText("alert/info"),msg:"선택한 탭 컨텍스트를 삭제하시겠습니까?",buttons:Ext.Msg.OKCANCEL,icon:Ext.Msg.QUESTION,fn:function(button) {
				if (button == "ok") {
					Ext.Msg.wait(Admin.getText("action/working"),Admin.getText("action/wait"));
					$.send(ENV.getProcessUrl("tab","@deleteContext"),{parent:parent,tab:tab},function(result) {
						if (result.success == true) {
							Ext.Msg.show({title:Admin.getText("alert/info"),msg:Admin.getText("action/worked"),buttons:Ext.Msg.OK,icon:Ext.Msg.INFO,fn:function() {
								Ext.getCmp("ModuleTabGroupList").selected = parent;
								Ext.getCmp("ModuleTabGroupList").getStore().reload();
							}});
						}
					});
				}
			}});
		}
	}
};