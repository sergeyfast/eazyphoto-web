var vfs = (function () {
	var path,
		targetHtmlElement,
		foldersViewMode,
		filesViewMode,
		currentFolderId,
		searchQuery,
		filesPage,
		currentFilePage,
		sortField,
		isDescending,
		selectedFiles = [], 	// список выбранных файлов
		cutFiles = [], 		// список вырезанных файлов
		cutFolder = -1, 		// вырезанная папка. -1 для индикации того, что папка не вырезана
		сutFolderId = -1, 	// от куда вырезали файлы или папку. -1 если операция неактивна
		data = {},
		el,
		elementControls,
		imageExt = [ 'png', 'jpg', 'jpeg' ],
		// ICanHaz Templates
		vfsBase,
		vfsStatus,
		vfsBreadcrumbs,
		vfsFolderList,
		vfsFileList,
		vfsDropdownControl,
		vfsPaginator,
		vfsPopup,
        uploadifyPath,
        uploadifyData,

		renderLayout = function () {
			vfsBase = ich.vfsBase(data);
			vfsStatus = ich.vfsStatus();
			el = {
				layout: ich.vfsBase(data),
				status: vfsBase.find('.vfs_status'),
				breadcrumbs: vfsBase.find('.vfs_breadcrumbs'),
				folders: vfsBase.find('.vfs_folder_list'),
				files: vfsBase.find('.vfs_file_list'),
				paginator: vfsBase.find('.vfs_paginator'),
				buttonViewFavorites: vfsBase.find('li._button_view_favorites'),
				searchInput: vfsBase.find('input._search_input'),
				buttonViewThumb: vfsBase.find('li._button_view_thumb'),
				buttonViewList: vfsBase.find('li._button_view_list'),
				buttonFolderNew: vfsBase.find('li._button_new_folder'),
				buttonFilePaste: vfsBase.find('li._button_paste'),
				buttonFileCut: vfsBase.find('li._button_cut'),
				buttonFileDelete: vfsBase.find('li._button_delete'),
				buttonFileUpload: vfsBase.find('li._button_add'),
				popupWrap: vfsBase.find('.vfs_popup_wrap')
			};

			el.buttonFolderNew.click(function () {
				var tmpInput;
				vfsPopup = ich.vfsFolderNew(data);
				tmpInput = vfsPopup.find('input');
				vfsPopup.find('.vfs_popup_overlay').click(function () {
					tmpInput.focus();
				});
				tmpInput.keydown(function (e) {
					var code = (e.keyCode ? e.keyCode : e.which);
					if (code == 13) {
						createFolder(tmpInput.val());
						destroyPopup();
					}
				});
				vfsPopup.find('._accept').click(function () {
					createFolder(tmpInput.val());
					destroyPopup();
				});
				vfsPopup.find('._cancel').click(function () {
					destroyPopup();
				});
				renderPopup(vfsPopup);
				tmpInput.focus();
			});

			el.searchInput.keyup(function (e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                searchFiles($(this).val());
                /*
                 if (code == 13) {
                    searchFiles($(this).val());
                 } else if ($(this).val().length > 2) {
                    searchFiles($(this).val());
                 }
                 */
			});

			setButtonViewFavorites();
			el.buttonViewFavorites.click(function () {
				if (foldersViewMode) {
					foldersViewMode = 0;
				} else {
					foldersViewMode = 1;
				}
				setButtonViewFavorites();
				renderData();
			});

			setButtonViewFiles();
			el.buttonViewThumb.add(el.buttonViewList).click(function () {
				if (!$(this).hasClass('_active')) {
					if (filesViewMode) {
						filesViewMode = 0;
					} else {
						filesViewMode = 1;
					}
					removeFilesFromSelected([], true);
					setButtonViewFiles();
					renderData();
				}
			});

			el.buttonFilePaste.click(function () {
				if ($(this).hasClass('_active') && сutFolderId != currentFolderId) {
					if (cutFolder === -1) {
						pasteFiles();
					} else if (cutFolder != currentFolderId) {
						pasteFolder();
					}
				}
			});

			el.buttonFileCut.click(function () {
				if ($(this).hasClass('_active')) {
					cutSelectedFiles();
				}
			});

			el.buttonFileDelete.click(function () {
				if ($(this).hasClass('_active')) {
				vfsPopup = ich.vfsFileDelete({ fileName: false });
				vfsPopup.find('._accept').click(function () {
					deleteFiles();
					destroyPopup();
				});
				vfsPopup.find('._cancel').click(function () {
					destroyPopup();
				});
				renderPopup(vfsPopup);
				}
			});

			el.buttonFileUpload.click(function () {
				vfsPopup = ich.vfsFileUpload(data);
				vfsPopup.find('._cancel').click(function () {
					destroyPopup();
					renderData();
				});
				renderPopup(vfsPopup);

                uploadifyData['folderId']  = currentFolderId;

				$('#vfs_uploadify').uploadify({
					'formData': uploadifyData,
                    'swf': uploadifyPath,
                    'uploader': data.uploadPath, // 'uploadify.php'
					'removeCompleted': true,
					'removeTimeout': 1,
					'buttonText': 'Выберите файлы',
					'height': 26,
					'onQueueComplete': function(queueData) {
						destroyPopup();
						renderData();
					}
				});
			});

			el.status.empty().append(vfsStatus);
			$(targetHtmlElement).empty().append(vfsBase);
		},

		renderData = function () {
			setStatusProcessing();
			getData(function () {
				setStatusSuccess();
				renderFolderList();
				renderFileList();
				renderBreadcrumbs();
				renderPaginator();
			});
		},

		renderBreadcrumbs = function () {
			vfsBreadcrumbs = ich.vfsBreadcrumbs(data);
			folderListEvents(vfsBreadcrumbs.find('a'));
			el.breadcrumbs.empty().append(vfsBreadcrumbs);
		},

		renderPaginator = function () {
			vfsPaginator = ich.vfsPaginator(data);
			paginatorEvents();
			el.paginator.empty().append(vfsPaginator);
		},

		resetPaginator = function () {
			currentFilePage = 0;
		},

		renderFolderList = function () {
			vfsFolderList = foldersViewMode ? ich.vfsFolderListFavs(data) : ich.vfsFolderList(data);
			folderListEvents(vfsFolderList.find('a'));
			elementControlsEvents(vfsFolderList.find('._element_controls'));
			el.folders.empty().append(vfsFolderList);
		},

		renderFileList = function () {
			var tmpSortTarget;
			var tmpSortClass = '_sorted';
			vfsFileList = filesViewMode ? ich.vfsFileListThumb(data) : ich.vfsFileList(data);
			fileListSelectEvents();
			fileListSortEvents();
			if (sortField === 'title') {
				tmpSortTarget = '._file_name';
			} else if (sortField === 'fileSize') {
				tmpSortTarget = '._file_size';
			} else if (sortField === 'createdAt') {
				tmpSortTarget = '._file_date';
			}
			if (isDescending) tmpSortClass = tmpSortClass + ' _isDescending';
			el.sortElements.parent().filter(tmpSortTarget).find('strong').addClass(tmpSortClass);
			elementControlsEvents(vfsFileList.find('._element_controls'));
			el.files.empty().append(vfsFileList);
		},

		paginatorEvents = function () {
			vfsPaginator.find('a').click(function (e) {
				e.preventDefault();
				if (data.filesShown < data.countFiles) {
					var tmpPage = 0;
					var tmpPageSize = data.countFiles;
					if ($(this).attr('href') === '#LoadPage') {
						tmpPage = currentFilePage = currentFilePage + 1;
						tmpPageSize = filesPage;
					} else if ($(this).attr('href') === '#LoadAll') {
						currentFilePage = Math.floor(data.countFiles / filesPage);
						data.fileList = [];
					}
					setStatusProcessing();
					$.jsonRPC.batchRequest(
						[
							{ method: 'CountFiles', params: [currentFolderId, searchQuery] },
							{ method: 'GetFiles',
								params: {
									folderId: currentFolderId,
									query: searchQuery,
									sortField: sortField,
									isDescending: isDescending,
									page: tmpPage,
									pageSize: tmpPageSize
								}
							}
						],
						{
							success: function (result) {
								data.countFiles = result[0].result;
								mapFileList(result[1].result);
								renderFileList();
								renderPaginator();
								setStatusSuccess();
							},
							error: function(result) {
								setStatusError(result);
							}
						});
					}
			});
		},

		folderListEvents = function (elem) {
			elem.click(function (e) {
				e.preventDefault();
				if (currentFolderId != $(this).attr('rel')) {
					currentFolderId = $(this).attr('rel');
					resetPaginator();
					selectedFiles = [];
					renderData();
				}
			});
		},

		fileListSortEvents = function () {
			el.sortElements = vfsFileList.find('li > strong');
			el.sortElements.click(function (e) {
				e.preventDefault();
				if ($(this).hasClass('_sorted')) {
					if (isDescending) {
						isDescending = false;
					} else {
						isDescending = true;
					}
				} else {
					if ($(this).parent().hasClass('_file_name')) {
						sortField = 'title';
					} else if ($(this).parent().hasClass('_file_size')) {
						sortField = 'fileSize';
					} else if ($(this).parent().hasClass('_file_date')) {
						sortField = 'createdAt';
					}
				}
				resetPaginator();
				renderData();
			});
		},
        closeWindow = function() {

        },
		fileListSelectEvents = function () {
			vfsFileList.find('dt a').click(function (e) {
				// File selected
				e.preventDefault();
                e.stopPropagation();

                // new code
                if ( Feedback  ) {
                    var $sf = $(this).closest('dl');
                    Feedback( {
                        "path" : $sf.attr('rel')
                        , "id": $sf.data('id')
                        , "name" : $sf.data('name')
                        , "width" : $sf.data('width')
                        , "height" : $sf.data('height')
                        , "shortPath" : $sf.data('shortPath')
                    });
                }
                // eof new code
			});
			vfsFileList.find('li._file_name input._file_check').change(function () {
				var tmp = vfsFileList.find('dt input._file_check');
				if ($(this).prop('checked')) {
					tmp.prop('checked', true);
					addFilesToSelected(tmp, true);
				} else {
					tmp.prop('checked', false);
					removeFilesFromSelected([], true);
				}
			});
            vfsFileList.find('dt input._file_check').click(function (e) {
				//e.preventDefault();
				e.stopPropagation()
				var tmp = $(this);
				if (tmp.prop('checked')) {
					addFilesToSelected(tmp, false);
				} else {
					removeFilesFromSelected(tmp, false);
				}
			});
			vfsFileList.filter('dl').click(function (e) {
				var tmp = $(this).find('input._file_check');
				if (tmp.prop('checked')) {
					tmp.prop('checked', false);
					removeFilesFromSelected(tmp, false);
				} else {
					tmp.prop('checked', true);
					addFilesToSelected(tmp, false);
				}
			});
		},

		addFilesToSelected = function (elems, overwrite) {
			if (overwrite) selectedFiles = [];
			elems.each(function () {
				selectedFiles.push(parseInt($(this).closest('dl').attr('data-id'), 10));
			});
			updateSelectedFilesButtons();
		},

		removeFilesFromSelected = function (elems, overwrite) {
			if (overwrite) {
				selectedFiles = [];
			} else {
				elems.each(function () {
					selectedFiles.splice(selectedFiles.indexOf(parseInt($(this).closest('dl').attr('data-id'), 10)), 1);
				});
			}
			updateSelectedFilesButtons();
		},

		updateSelectedFilesButtons = function () {
			if (selectedFiles.length > 0) {
				el.buttonFileCut.addClass('_active');
				el.buttonFileDelete.addClass('_active');
			} else {
				el.buttonFileCut.removeClass('_active');
				el.buttonFileDelete.removeClass('_active');
			}
		},

		elementControlsEvents = function (elementControls) {
			elementControls.click(function (e) {
				e.stopPropagation();
				if ($(this).hasClass('_active')) {
					elementControls.removeClass('_active').children().detach();
				} else {
					elementControls.removeClass('_active').children().detach();
					if ($(this).closest('.vfs_cont_panel').hasClass('vfs_cont_panel_folders')) {
						vfsDropdownControl = ich.vfsFolderControl();
					} else {
						vfsDropdownControl = ich.vfsFileControl({ fileLink: $(this).closest('dl').attr('rel') });
					}
					vfsDropdownControl.find('li').click(function (e) {
						var tmp = $(this).closest('dl');
						e.preventDefault();
						switch ($(this).attr('class')) {
						case 'vfs_dropdown_favorite':
							manageFavorites(tmp);
							break;

						case 'vfs_dropdown_delete':
							if ($(this).closest('.vfs_cont_panel').hasClass('vfs_cont_panel_folders')) {
								vfsPopup = ich.vfsFolderDelete({ folderName: tmp.find('a').text() });
								vfsPopup.find('._accept').click(function () {
									deleteFolder(tmp.attr('rel'));
									destroyPopup();
								});
								vfsPopup.find('._cancel').click(function () {
									destroyPopup();
								});
								renderPopup(vfsPopup);
							} else {
								vfsFileList.find('input._file_check').prop('checked', false);
								$(this).closest('dl').find('input._file_check').prop('checked', true);
								addFilesToSelected($(this), true);
								vfsPopup = ich.vfsFileDelete({ fileName: tmp.find('a').text() });
								vfsPopup.find('._accept').click(function () {
									deleteFiles();
									destroyPopup();
								});
								vfsPopup.find('._cancel').click(function () {
									destroyPopup();
								});
								renderPopup(vfsPopup);
							}
							break;

						case 'vfs_dropdown_rename':
							if ($(this).closest('.vfs_cont_panel').hasClass('vfs_cont_panel_folders')) {
								vfsPopup = ich.vfsFolderRename({ folderName: tmp.find('a').text() });
								tmpInput = vfsPopup.find('input');
								vfsPopup.find('.vfs_popup_overlay').click(function () {
									tmpInput.focus();
								});
								tmpInput.keydown(function (e) {
									var code = (e.keyCode ? e.keyCode : e.which);
									if(code == 13) {
										renameFolder(tmp.attr('rel'), tmpInput.val());
										destroyPopup();
									}
								});
								vfsPopup.find('._accept').click(function () {
									renameFolder(tmp.attr('rel'), tmpInput.val());
									destroyPopup();
								});
								vfsPopup.find('._cancel').click(function () {
									destroyPopup();
								});
								renderPopup(vfsPopup);
								tmpInput.focus();
							} else {
								vfsPopup = ich.vfsFileRename({ name: tmp.attr('data-name'), ext: tmp.attr('data-ext'), relpath: tmp.attr('data-relpath') });
								tmpInput = vfsPopup.find('input');
								vfsPopup.find('.vfs_popup_overlay').click(function () {
									tmpInput.focus();
								});
								tmpInput.keydown(function (e) {
									var code = (e.keyCode ? e.keyCode : e.which);
									if(code == 13) {
										renameFile(tmp.attr('data-id'), tmpInput.val() + '.' + tmp.attr('data-ext'));
										destroyPopup();
									}
								});
								vfsPopup.find('._accept').click(function () {
									renameFile(tmp.attr('data-id'), tmpInput.val() + '.' + tmp.attr('data-ext'));
									destroyPopup();
								});
								vfsPopup.find('._cancel').click(function () {
									destroyPopup();
								});
								renderPopup(vfsPopup);
								tmpInput.focus();
							}
							break;

						case 'vfs_dropdown_cut':
							if ($(this).closest('.vfs_cont_panel').hasClass('vfs_cont_panel_folders')) {
								cutSelectedFolder(parseInt(tmp.attr('rel'), 10));
							} else {
								vfsFileList.find('input._file_check').prop('checked', false);
								addFilesToSelected($(this), true);
								cutSelectedFiles();
							}
							break;

						case 'vfs_dropdown_link':
							var tmpInput;
							vfsPopup = ich.vfsFileLink({ path: tmp.attr('rel') });
							tmpInput = vfsPopup.find('input');
							vfsPopup.find('.vfs_popup').click(function () {
								tmpInput.prop('readonly', false).focus().prop('readonly', true);
							});
							vfsPopup.find('.vfs_popup_overlay').click(function () {
								destroyPopup();
							});
							vfsPopup.find('._cancel').click(function () {
								destroyPopup();
							});
							renderPopup(vfsPopup);
							tmpInput.focus().prop('readonly', true);
						}
					});
					$(this).addClass('_active').append(vfsDropdownControl);
				}
			});
		},

		renderPopup = function (tmplt) {
			el.popupWrap.empty().append(tmplt);
		},

		destroyPopup = function () {
			el.popupWrap.empty();
		},

		getData = function (callback) {
			$.jsonRPC.batchRequest(
				[
					{ method: 'GetFolder', params: [currentFolderId] },
					{ method: 'CountFiles', params: [currentFolderId, searchQuery] },
					{ method: 'GetFiles',
						params: {
							folderId: currentFolderId,
							query: searchQuery,
							sortField: sortField,
							isDescending: isDescending,
							page: 0,
							pageSize: filesPage * (currentFilePage + 1)
						}
					},
					{ method: 'GetFavorites' },
					{ method: 'GetFolderBranch', params: [currentFolderId] },
					{ method: 'HelpUpload' }
				],
				{
					success: function (result) {
						removeFilesFromSelected([], true);
						data = {
							folderId: 			result[0].result.id,
							folderParentId: 	result[0].result.parentId,
							folderName: 		result[0].result.name,
							countFiles: 		result[1].result,
							favorites: 			result[3].result,
							favoritesList:		[],
							folderList:			[],
							fileList: 			[],
							folderBranch: 		[],
							filesPage:			filesPage,
							uploadPath: 		result[5].result.queue.url
						}

						if (data.favorites) {
							$.map(data.favorites, function (val, key) {
								val.isFavorite = data.favorites[key] ? '_is_favorite' : '';
								data.favoritesList.push(val);
							});
						}

						if (result[0].result.folders) {
							$.map(result[0].result.folders, function (val, key) {
								val.isFavorite = data.favorites[key] ? '_is_favorite' : '';
								data.folderList.push(val);
							});
						}

						if (result[2].result) {
							mapFileList(result[2].result);
						}

						if (result[4].result) {
							$.map(result[4].result, function (val, key) {
								data.folderBranch.push(val);
							});
						}

						if (callback && typeof(callback) === "function") {
							callback();
						}
					},
					error: function (result) {
						setStatusError(result);
					}
				}
			);
		},

		mapFileList = function (result) {
			$.map(result, function (val, key) {
				if ($.inArray(val.extension, imageExt) > -1) {
					val.isImage = '_file_is_image';
				}
				val.date = val.date.slice(0, -6);
				val.size = bytesToSize(val.size, 2);
				data.fileList.push(val);
				data.filesShown = filesPage * (currentFilePage + 1) < data.countFiles ? filesPage * (currentFilePage + 1) : data.countFiles
				data.filesLeft = data.countFiles - data.filesShown;
				if (data.filesLeft > filesPage) data.filesLeft = filesPage;
			});
		},

		setStatusProcessing = function () {
			el.status.removeClass('_success _error').text('Processing…');
		},

		setStatusError = function (result) {
			el.status.removeClass('_success').addClass('_error').text('Error!');
			console.log(result);
		},

		setStatusSuccess = function () {
			el.status.removeClass('_error').addClass('_success').text('Success');
		},

		setButtonViewFavorites = function () {
			if (foldersViewMode) {
				el.buttonViewFavorites.addClass('_active');
			} else {
				el.buttonViewFavorites.removeClass('_active');
			}
		},

		setButtonViewFiles = function () {
			if (filesViewMode) {
				el.buttonViewThumb.addClass('_active');
				el.buttonViewList.removeClass('_active');
			} else {
				el.buttonViewThumb.removeClass('_active');
				el.buttonViewList.addClass('_active');
			}
		},

		renameFile = function (id, val) {
			$.jsonRPC.request('SetFilePhysicalName', {
				params: { fileId: id, name: val },
				success: function (result) {
					renderData();
				},
				error: function(result) {
					setStatusError(result);
				}
			});
		},

		manageFavorites = function (elem) {
			var favState;
			if (elem.find('._folderState').hasClass('_is_favorite')) {
				favState = false;
			} else {
				favState = true;
			}
			setStatusProcessing();
			$.jsonRPC.request('ManageFavorites', {
					params: { 'folderId': elem.attr('rel'), 'isInFavorites': favState },
				success: function (result) {
					renderData();
				},
				error: function(result) {
					setStatusError(result);
				}
			});
		},

		createFolder = function (val) {
			$.jsonRPC.request('CreateFolder', {
				params: {rootFolderId: currentFolderId, name: val },
				success: function (result) {
					renderData();
				},
				error: function(result) {
					setStatusError(result);
				}
			});
		},

		renameFolder = function (id, val) {
			$.jsonRPC.request('RenameFolder', {
				params: { folderId: id, name: val },
				success: function (result) {
					renderData();
				},
				error: function(result) {
					setStatusError(result);
				}
			});
		},

		deleteFolder = function (id) {
			$.jsonRPC.request('DeleteFolder', {
				params: { folderId: id },
				success: function (result) {
					renderData();
				},
				error: function(result) {
					setStatusError(result);
				}
			});
		},

		deleteFiles = function () {
			$.jsonRPC.request('DeleteFiles', {
				params: { fileIds: selectedFiles },
				success: function (result) {
					renderData();
				},
				error: function(result) {
					setStatusError(result);
				}
			});
		},

		cutSelectedFolder = function (id) {
			cutFolder = id;
			сutFolderId = currentFolderId;
			el.buttonFilePaste.addClass('_active');
			vfsFileList.find('input._file_check').prop('checked', false);
			removeFilesFromSelected([], true);
		},

		pasteFolder = function () {
			$.jsonRPC.request('MoveFolder', {
				params: { folderId: cutFolder, destinationFolderId: currentFolderId },
				success: function (result) {
					renderData();
					cutFiles = [];
					cutFolder = -1;
					сutFolderId = -1;
					el.buttonFilePaste.removeClass('_active');
				},
				error: function(result) {
					setStatusError(result);
				}
			});
		},

		cutSelectedFiles = function () {
			cutFiles = selectedFiles;
			сutFolderId = currentFolderId;
			el.buttonFilePaste.addClass('_active');
			vfsFileList.find('input._file_check').prop('checked', false);
			removeFilesFromSelected([], true);
			cutFolder = -1;
		},

		pasteFiles = function () {
			$.jsonRPC.request('MoveFiles', {
				params: { fileIds: cutFiles, destinationFolderId: currentFolderId },
				success: function (result) {
					renderData();
					cutFiles = [];
					cutFolder = -1;
					сutFolderId = -1;
					el.buttonFilePaste.removeClass('_active');
				},
				error: function(result) {
					setStatusError(result);
				}
			});
		},

		searchFiles = function (query) {
			setStatusProcessing();
			searchQuery = query;
			$.jsonRPC.batchRequest(
				[
					{ method: 'CountFiles', params: [currentFolderId, searchQuery] },
					{ method: 'GetFiles',
						params: {
							folderId: currentFolderId,
							query: searchQuery,
							sortField: sortField,
							isDescending: isDescending,
							page: currentFilePage,
							pageSize: filesPage
						}
					}
				],
				{
					success: function (result) {
						data.fileList = [];
						resetPaginator();
						data.countFiles = result[0].result;
						mapFileList(result[1].result);
						renderFileList();
						renderPaginator();
						setStatusSuccess();
					},
					error: function(result) {
						setStatusError(result);
					}
				});
		}

        , setStartFolderId = function ( startFileId, startFile ) {
            if ( startFileId ) {
                $.jsonRPC.request('SearchFolderByFileId', {
                    async: false,
                    params: { fileId: startFileId },
                    success: function (result) {
                        currentFolderId = result.result.id;
                    }
                });
            } else if ( startFile ) {
                $.jsonRPC.request('SearchFolderByFile', {
                    params: { filename: startFile },
                    async: false,
                    success: function (result) {
                        currentFolderId = ( result.result ) ? result.result.id : 1;
                    }
                });
            }
        },

		bytesToSize = function (bytes, precision) {
			var kilobyte = 1024;
			var megabyte = kilobyte * 1024;
			var gigabyte = megabyte * 1024;
			if ((bytes >= 0) && (bytes < kilobyte)) {
				return bytes + '&thinsp;<span>B</span>';
			} else if ((bytes >= kilobyte) && (bytes < megabyte)) {
				return (bytes / kilobyte).toFixed(precision) + '&thinsp;<span>KB</span>';
			} else if ((bytes >= megabyte) && (bytes < gigabyte)) {
				return (bytes / megabyte).toFixed(precision) + '&thinsp;<span>MB</span>';
			} else if ((bytes >= gigabyte) && (bytes < terabyte)) {
				return (bytes / gigabyte).toFixed(precision) + '&thinsp;<span>GB</span>';
			} else {
				return bytes + '&thinsp;<span>B</span>';
			}
		},

		init = function (vfsInit) {
			path = vfsInit.path;
			currentFolderId = vfsInit.folder || 1;
			targetHtmlElement = vfsInit.target || '#vfs_target';
			searchQuery = vfsInit.searchQuery || '';
			filesPage = vfsInit.filesPage || 100;
			currentFilePage = vfsInit.currentPage || 0;
			foldersViewMode = vfsInit.foldersView || 0; // Default view: 0. Favorites view: 1.
			filesViewMode = vfsInit.filesView || 0; // Default view: 0. Thumbnails view: 1.
			filesViewMode = vfsInit.filesView || 0; // Default view: 0. Thumbnails view: 1.
			sortField = vfsInit.sortField || 'createdAt';
			isDescending = vfsInit.isDescending || true;
            uploadifyPath = vfsInit.uploadifyPath || 'uploadify.swf';
            uploadifyData = vfsInit.uploadifyData || {};

            $.jsonRPC.setup({
                endPoint: path,
                namespace: ''
            });


            setStartFolderId( vfsInit.startFileId || null, vfsInit.startFile || null );
            renderLayout();
            renderData();
		};

	return {
		init: init
	}
}());
