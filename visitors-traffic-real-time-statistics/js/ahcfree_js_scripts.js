
var colors = ['#DB6946', '#C14543', '#445060', '#395953', '#6C8C80', '#829AB5', '#BF807A', '#BF0000', '#006BB7', '#EC732C', '#BF3D27', '#A6375F',
	'#8C6D46', '#326149', '#802B35', '#8A3842', '#366D73', '#4D6173', '#4A4659', '#C9D65B', '#F45552', '#F3CC5E', '#F29B88', '#D96941',
	'#484F73', '#C9AB81', '#F5655C', '#F0C480'];

jQuery(document).ready(function () {




	if (typeof google === 'object' && typeof google.maps === 'object') {
		return;
	} else {

	}



});
//------------------------------------------------------------------------------				
function convertToNumeric(data) {
	if (data instanceof Array) {
		for (var index in data) {
			data[index] = Number(data[index]);
		}
	} else {
		data = Number(data);
	}
	return data;
}
//------------------------------------------------------------------------------
function getRandomElementFromArray(array) {
	var ranIndex = Math.floor(Math.random() * array.length);
	return array[ranIndex];
}
//------------------------------------------------------------------------------
function drawVisitsLineChart(visitsData) {
	var randomScalingFactor = function () { return Math.round(Math.random() * 100) };

	var barChartData = {
		labels: visitsData.data.dates,
		datasets: [
			{
				label: "Visitors",
				barShowStroke: false,
				fillColor: "rgba(75,178,1970,.5)",
				strokeColor: "rgba(75,178,1970,.5)",
				highlightFill: "rgba(220,220,220,0.75)",
				highlightStroke: "rgba(220,220,220,1)",
				data: visitsData.data.visitors
			},
			{
				label: "Visits",
				barShowStroke: false,
				fillColor: "rgba(234,162,40,0.5)",
				strokeColor: "rgba(234,162,40,0.5)",
				highlightFill: "rgba(151,187,205,0.75)",
				highlightStroke: "rgba(151,187,205,1)",
				data: visitsData.data.visits
			}
		]

	}
	var ctx = document.getElementById("visitorsVisitsChart").getContext("2d");
	window.myBar = new Chart(ctx).Bar(barChartData, {
		responsive: true
	});
}
//------------------------------------------------------------------------------





function isEmpty(val) {
	return (val == null || val == 0 || val == '' || val == '0');
}

//------------------------------------------------------------------------------
function countVisits(arr) {
	var count = 0;
	for (var i = 0; i < arr.length; i++) {
		count += Number(arr[i]);
	}
	return count;
}
//------------------------------------------------------------------------------

jQuery(document).ready(function () {

	//------------------------------------------
	//if(visitsData.success && typeof visitsData.data != 'undefined'){
	//var duration = jQuery('#hits-duration').val();
	//drawVisitsLineChart( mystart_date, myend_date, '1 day', visitors_data, visits_data, duration );
	//}
	//------------------------------------------

	//------------------------------------------

	//------------------------------------------
	jQuery.fn.dataTable.ext.errMode = 'none';
	/* Optimized Traffic by Title - DataTable Implementation */
	if (jQuery('#traffic_by_title').length) {
		trafficByTitleTableOptimized();
	}

	function trafficByTitleTableOptimized() {
		jQuery('#traffic_by_title').DataTable({
			"pageLength": 10,
			"searching": true,
			"ordering": false,
			"bLengthChange": false,
			"bFilter": true,
			"bInfo": false,
			"bAutoWidth": false,
			"bJQueryUI": true,
			"processing": true,
			"serverSide": true,
			ajax: ahc_ajax.ajax_url + '?action=traffic_by_title',
			dataSrc: 'data',
			columns: [
				{ data: 'rank', defaultContent: '' },
				{ data: 'til_page_title', defaultContent: '' },
				{
					data: 'til_hits',
					defaultContent: '0',
					render: function (data, type, row) {
						if (type === 'display') {
							var postId = row.til_page_id || 0;
							var hits = parseInt(data) || 0;
							var pageTitle = '';

							// Extract clean title from HTML
							if (row.til_page_title) {
								var tempDiv = document.createElement('div');
								tempDiv.innerHTML = row.til_page_title;
								pageTitle = tempDiv.textContent || tempDiv.innerText || 'Unknown';
							}

							// Simple clickable hits with modal trigger
							return `<div class="ahc-stats-cell">
                            <button type="button" 
                                    class="ahc-stats-button" 
                                    data-toggle="modal" 
                                    data-target="#TrafficStatsModal"
                                    data-post-id="${postId}" 
                                    data-page-title="${pageTitle.replace(/"/g, '&quot;')}"
                                    data-hits="${hits}">
                                <div class="dashicons ahc-icon">&nbsp;<span style="font-size:14px">${hits.toLocaleString()}</span></div>
                                
                            </button>
                        </div>`;
						}
						return data;
					}
				},
				{ data: 'percent', defaultContent: '0%' }
			],
			language: {
				searchPlaceholder: "Search titles...",
				processing: "<span class='loader'>&nbsp;</span>",
				"zeroRecords": "No data available.",
				paginate: {
					next: '<i class="dashicons dashicons-arrow-right-alt2"></i>',
					previous: '<i class="dashicons dashicons-arrow-left-alt2"></i>'
				}
			},
			"fnDrawCallback": function (oSettings) {
				if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
					jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
				}
			},
			dom: 'Bfrtip',
			buttons: [{
				extend: 'excelHtml5',
				title: "",
				action: function (e, dt, node, config) {
					var processing = jQuery("#traffic_by_title").parents(".panelcontent").find(".dataTables_processing");
					processing.show();

					jQuery.ajax({
						url: ahc_ajax.ajax_url + '?action=traffic_by_title&page=all',
						data: dt.ajax.params(),
						success: function (res, status, xhr) {
							try {
								// If server returns false, use current table data
								if (res === false || res === 'false' || !res) {
									var currentData = dt.rows().data().toArray();
									if (currentData && currentData.length > 0) {
										exportCurrentTableData(currentData);
									} else {
										alert('No data available for export.');
									}
									return;
								}

								var xlsRows;

								// Try to parse JSON
								if (typeof res === 'string') {
									xlsRows = JSON.parse(res);
								} else if (typeof res === 'object' && res.data) {
									xlsRows = res.data;
								} else if (Array.isArray(res)) {
									xlsRows = res;
								} else {
									xlsRows = res;
								}

								// Check if data is valid
								if (!xlsRows || !Array.isArray(xlsRows) || xlsRows.length === 0) {
									var currentData = dt.rows().data().toArray();
									if (currentData && currentData.length > 0) {
										exportCurrentTableData(currentData);
									} else {
										alert('No data available for export.');
									}
									return;
								}

								exportServerData(xlsRows);

							} catch (error) {
								// Fallback: export current table data
								try {
									var currentData = dt.rows().data().toArray();
									if (currentData && currentData.length > 0) {
										exportCurrentTableData(currentData);
									} else {
										alert('Export failed and no table data available.');
									}
								} catch (fallbackError) {
									alert('Export failed. Please try again.');
								}
							} finally {
								processing.hide();
							}
						},
						error: function (xhr, status, error) {
							processing.hide();

							// Fallback: export current table data
							try {
								var currentData = dt.rows().data().toArray();
								if (currentData && currentData.length > 0) {
									exportCurrentTableData(currentData);
								} else {
									alert('Export failed and no table data available.');
								}
							} catch (fallbackError) {
								alert('Export failed. Please try again.');
							}
						}
					});

					// Function to export server data
					function exportServerData(xlsRows) {
						var xlsHeader = ["Rank", "Title", "Hits", "Percentage"];
						var createXLSLFormatObj = [xlsHeader];

						jQuery.each(xlsRows, function (index, value) {
							var cleanTitle = value.til_page_title || value.title || '';

							// Remove HTML tags from title
							if (cleanTitle && typeof cleanTitle === 'string') {
								var tempDiv = document.createElement('div');
								tempDiv.innerHTML = cleanTitle;
								cleanTitle = tempDiv.textContent || tempDiv.innerText || cleanTitle;
							}

							var innerRowData = [
								value.rank || index + 1,
								cleanTitle,
								value.til_hits || value.hits || 0,
								value.percent || value.percentage || '0%'
							];
							createXLSLFormatObj.push(innerRowData);
						});

						createExcelFile(createXLSLFormatObj);
					}

					// Function to export current table data (fallback)
					function exportCurrentTableData(tableData) {
						var xlsHeader = ["Rank", "Title", "Hits", "Percentage"];
						var createXLSLFormatObj = [xlsHeader];

						jQuery.each(tableData, function (index, rowData) {
							var cleanTitle = rowData.til_page_title || rowData.title || '';

							// Remove HTML tags from title
							if (cleanTitle && typeof cleanTitle === 'string') {
								var tempDiv = document.createElement('div');
								tempDiv.innerHTML = cleanTitle;
								cleanTitle = tempDiv.textContent || tempDiv.innerText || cleanTitle;
							}

							var innerRowData = [
								rowData.rank || index + 1,
								cleanTitle,
								rowData.til_hits || rowData.hits || 0,
								rowData.percent || rowData.percentage || '0%'
							];
							createXLSLFormatObj.push(innerRowData);
						});

						createExcelFile(createXLSLFormatObj);
					}

					// Function to create Excel file
					function createExcelFile(data) {
						var wb = XLSX.utils.book_new();
						var ws = XLSX.utils.aoa_to_sheet(data);
						XLSX.utils.book_append_sheet(wb, ws, "Traffic by Title");
						XLSX.writeFile(wb, "traffic_by_title.xlsx");
					}
				}
			}]
		});

		// Simple modal event handler - no complex data generation
		jQuery(document).on('click', 'button[data-target="#TrafficStatsModal"]', function (e) {
			e.preventDefault();

			var button = jQuery(this);
			var postId = button.data('post-id') || 0;
			var pageTitle = button.data('page-title') || 'Page Statistics';
			var hits = button.data('hits') || 0;

			// Clean the page title
			if (pageTitle.includes('http')) {
				pageTitle = pageTitle.split('http')[0].trim();
			}
			pageTitle = pageTitle.replace(/[\/\s]*$/, '');

			if (!pageTitle || pageTitle.length < 2) {
				pageTitle = 'Page Statistics';
			}

			// Clean, professional modal content matching Recent Visitors style
			var headerHtml = `
            <style>
                /* Modal styling - matching Recent Visitors */
                #TrafficStatsModal .modal-dialog {
                    max-width: 900px !important;
                    width: 85vw !important;
                    margin: 30px auto !important;
                }
                
                @media (max-width: 768px) {
                    #TrafficStatsModal .modal-dialog {
                        width: 95vw !important;
                        margin: 10px auto !important;
                    }
                }
                
                #TrafficStatsModal .modal-content {
                    border-radius: 12px !important;
                    box-shadow: 0 8px 32px rgba(40,60,120,0.18) !important;
                }
                
                .traffic-header-enhanced {
                    display: flex; 
                    gap: 10px; 
                    align-items: center; 
                    margin-bottom: 20px; 
                    background: #f8fafc; 
                    border-radius: 10px; 
                    padding: 12px 16px; 
                    box-shadow: 0 2px 8px rgba(0,0,0,0.04); 
                    font-family: 'Segoe UI', 'Arial', sans-serif; 
                    font-size: 0.97em;
                }
                
                .traffic-header-enhanced .page-title { 
                    font-weight: bold; 
                    font-size: 1em; 
                    color: #222; 
                    flex: 1;
                }
                
                .traffic-header-enhanced .hits { 
                    color: #2563eb; 
                    font-size: 0.95em; 
                    background: #e0e7ef; 
                    border-radius: 4px; 
                    padding: 2px 8px; 
                    margin-left: 2px; 
                    font-weight: 500; 
                }
                
                .traffic-image-container {
                    margin-top: 20px;
                    text-align: center;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }
                
                .traffic-image-container img {
                    width: 100%;
                    height: auto;
                    display: block;
                }
            </style>
            <div class="traffic-header-enhanced">
                <div class="page-title"></div>
                <div class="hits">${hits.toLocaleString()} hits</div>
            </div>
            <div class="traffic-image-container">
                <a target="_blank" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029">
                    <img src="${ahc_ajax.plugin_url}images/Traffic_by_Title_PRO.jpg" alt="Traffic by Title PRO" />
                </a>
            </div>
        `;

			// Update modal content and title
			jQuery('#TrafficStatsModal .modal-body').html(headerHtml);
			jQuery('#TrafficStatsModal .modal-body').html(headerHtml);
			jQuery('#TrafficStatsModal .modal-body .page-title').text(pageTitle);
			jQuery('#TrafficStatsModal .modal-title').text('Page Statistics: ' + pageTitle);

			// Show modal
			jQuery('#TrafficStatsModal').modal('show');
		});

		// Enhanced modal close functionality
		jQuery(document).on('click', '[data-dismiss="modal"], .close', function () {
			jQuery('#TrafficStatsModal').modal('hide');
		});

		// Backdrop click to close modal
		jQuery(document).on('click', '#TrafficStatsModal', function (e) {
			if (e.target === this) {
				jQuery('#TrafficStatsModal').modal('hide');
			}
		});

		// ESC key to close modal
		jQuery(document).on('keydown', function (e) {
			if (e.keyCode === 27 && jQuery('#TrafficStatsModal').is(':visible')) {
				jQuery('#TrafficStatsModal').modal('hide');
			}
		});

		// Reset modal content when closed
		jQuery('#TrafficStatsModal').on('hide.bs.modal', function (event) {
			jQuery('.modal-body').html('<div class="ahc_loader" style="width:100px !important; height:50px !important;">&nbsp;</div>');
			jQuery('.modal-title').text('Page Statistics');
		});
	}

	// Simple CSS for the stats button
	jQuery(document).ready(function ($) {
		// Add CSS for the stats button
		var buttonStyles = `
        <style>
            .ahc-stats-cell {
                text-align: center;
            }
            .ahc-stats-button {
                background: none;
                border: none;
                cursor: pointer;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 2px;
                padding: 4px;
                border-radius: 4px;
                transition: background-color 0.2s;
                color: #2271b1;
                text-decoration: none;
            }
            .ahc-stats-button:hover {
                background-color: #f0f6ff;
            }
            .ahc-stats-button .ahc-icon:before {
                content: "\\f185";
                color: #1DAE22;
                font-family: dashicons;
                font-size: 16px;
            }
            .ahc-stats-button .stat-number {
                font-size: 13px;
                font-weight: 500;
            }
        </style>
    `;

		$('head').append(buttonStyles);
	});
	if (jQuery('#traffic_sources_table').length && jQuery('#traffic_sources_table tbody tr').length > 0) {
		jQuery('#traffic_sources_table').DataTable({
			"pageLength": 10,
			"searching": false,
			"ordering": true,
			"bLengthChange": false,
			"bFilter": true,
			"bInfo": false,
			"bJQueryUI": true,
			"order": [[0, "asc"]],
			language: {
				"zeroRecords": "No traffic sources data available.",
				paginate: {
					next: '<i class="dashicons dashicons-arrow-right-alt2"></i>',
					previous: '<i class="dashicons dashicons-arrow-left-alt2"></i>'
				}
			},
			"fnDrawCallback": function (oSettings) {
				if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
					jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
				}
			},
			columnDefs: [
				{
					targets: [1, 3],
					orderable: false
				}
			],
			dom: 'Bfrtip',
			buttons: [{
				extend: 'excelHtml5',
				title: "Traffic Sources Report",
				filename: "Traffic_Sources_Report",
				exportOptions: {
					columns: [0, 1, 2, 3, 4],
					format: {
						body: function (data, row, col, node) {
							// Strip HTML tags and clean text
							const tempDiv = document.createElement("div");
							tempDiv.innerHTML = data;
							let cleanText = tempDiv.textContent || tempDiv.innerText || "";

							// Clean up whitespace
							cleanText = cleanText.replace(/\s+/g, ' ').trim();

							return cleanText;
						}
					}
				}
			}]
		});
	}
	if (jQuery('#lasest_search_words').length) {
		latestSearchTable();
	}
	function latestSearchTable() {
		jQuery('#lasest_search_words').DataTable({
			"pageLength": 10,
			"searching": false,
			"ordering": false,
			"bLengthChange": false,
			"bFilter": true,
			"bInfo": false,
			"bJQueryUI": true,
			"processing": true,
			"serverSide": true,
			ajax: ahc_ajax.ajax_url + '?action=latest_search_words&fdt=' + jQuery("#from_dt").val() + "&tdt=" + jQuery("#to_dt").val(),
			dataSrc: 'data',
			columnDefs: [{
				targets: 1,
				className: 'hide'
			}],
			columns: [
				{ data: 'img' },
				{ data: 'csb' },
				{ data: 'keyword' },
				{ data: 'dt' },
			],
			language: {
				processing: "<span class='loader'>&nbsp;</span>",
				"zeroRecords": "No data available.",
				paginate: {
					next: '<i class="dashicons dashicons-arrow-right-alt2"></i>',
					previous: '<i class="dashicons dashicons-arrow-left-alt2"></i>'
				}
			},
			"fnDrawCallback": function (oSettings) {
				if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
					jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
				}
			},
			dom: 'Bfrtip',
			buttons: [{
				extend: 'excelHtml5',
				title: "",
				action: function (e, dt, node, config) {
					jQuery("#lasest_search_words").parents(".panelcontent").find(".dataTables_processing").show();
					jQuery.ajax({
						url: ahc_ajax.ajax_url + '?action=latest_search_words&page=all&fdt=' + jQuery("#from_dt").val() + "&tdt=" + jQuery("#to_dt").val(),
						data: dt.ajax.params(),
						success: function (res, status, xhr) {
							//console.log(res);

							var createXLSLFormatObj = [];

							/* XLS Head Columns */
							var xlsHeader = ["Country/SE/Browser", "Keyword", "Date"];

							/* XLS Rows Data */
							var xlsRows = JSON.parse(res);

							createXLSLFormatObj.push(xlsHeader);
							jQuery.each(xlsRows, function (index, value) {
								var innerRowData = [];
								jQuery.each(value, function (ind, val) {
									innerRowData.push(val);
								});
								createXLSLFormatObj.push(innerRowData);
							});
							jQuery("#lasest_search_words").parents(".panelcontent").find(".dataTables_processing").hide();
							/* File Name */
							var filename = "latest_search_words.xlsx";

							/* Sheet Name */
							var ws_name = "sheet1";

							if (typeof console !== 'undefined') console.log(new Date());
							var wb = XLSX.utils.book_new(),
								ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);

							/* Add worksheet to workbook */
							XLSX.utils.book_append_sheet(wb, ws, ws_name);

							/* Write workbook and Download */
							if (typeof console !== 'undefined') console.log(new Date());
							XLSX.writeFile(wb, filename);
							if (typeof console !== 'undefined') console.log(new Date());

						}
					})
				},
				exportOptions: {
					columns: [1, 2, 3]
				},
			}],

		});

	}

	if (jQuery('#top_refering_sites').find("tr").length > 1) {
		jQuery('#top_refering_sites').DataTable({
			"pageLength": 10,
			"searching": false,
			"ordering": false,
			"bLengthChange": false,
			"bFilter": true,
			"bInfo": false,
			"bAutoWidth": false,
			language: {
				paginate: {
					next: '<i class="dashicons dashicons-arrow-right-alt2"></i>',
					previous: '<i class="dashicons dashicons-arrow-left-alt2"></i>'
				}
			},
			"fnDrawCallback": function (oSettings) {
				if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
					jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
				}
			},
			dom: 'Bfrtip',
			buttons: [{
				extend: 'excelHtml5',
				title: "Top Refering Sites"
			}]
		});
	}
	if(jQuery('#recent_visit_by_ip').length) {
		recentVisitorsOptimized();
	}
	
	function recentVisitorsOptimized() {
		// Wait a bit for DOM to be fully ready (like pro version)
		setTimeout(function() {
			initializeTable();
		}, 100);
	}
	
	function initializeTable() {
		// Check if table element exists
		if (jQuery('#recent_visit_by_ip').length === 0) {
			return;
		}
	
		// Function to get form values (send exact date range)
		function getFormValues() {
			var fromDate = jQuery("#r_from_dt").val() || '';
			var toDate = jQuery("#r_to_dt").val() || '';
			var ip = jQuery("#ip_addr").val() || '';
			
			return { 
				from_date: fromDate,
				to_date: toDate, 
				ip: ip 
			};
		}
	
		// Initialize DataTable (exact same structure as pro version)
		var table = jQuery('#recent_visit_by_ip').DataTable({
			"pageLength": 10,
			"searching": false,
			"ordering": false,
			"bLengthChange": false,
			"bFilter": false,
			"bInfo": false,
			"bAutoWidth": false,
			"bJQueryUI": true,
			"processing": true,
			"serverSide": true,
			"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			ajax: {
				url: ahc_ajax.ajax_url + '?action=recent_visitor_by_ip',
				type: 'GET',
				data: function(d) {
					var formValues = getFormValues();
					
					// Add the exact date range values
					d.from_date = formValues.from_date;
					d.to_date = formValues.to_date;
					d.ip = formValues.ip;
					
					return d;
				},
				dataSrc: 'data',
				error: function(xhr, error, thrown) {
					// Silent error handling
				}
			},
			columns: [
				{ data: 'hit_ip_address' },
				{ data: 'ctr_name' },
				{ data: 'time' },
				{ 
					data: 'duration',
					defaultContent: '00:00:01'
				},
				{
					data: 'day_hits',
					render: function (data, type, row, meta) {
						if (typeof data === 'string' && data.includes('<button')) {
							var buttonHtml = data;
							buttonHtml = buttonHtml.replace('<button', '<button ' +
								'data-time="' + (row.time || '') + '" ' +
								'data-ctr-name="' + (row.ctr_name || '') + '" ' +
								'data-ctr-code="' + (row.ctr_internet_code || 'eg') + '" ' +
								'data-city="' + (row.ahc_city || '') + '" ' +
								'data-region="' + (row.ahc_region || '') + '" ' +
								'data-duration="' + (row.duration || '00:00:01') + '" '
							);
							return buttonHtml;
						}
						
						return data;
					}
				}
			],
			language: {
				processing: "<span class='loader'>&nbsp;</span>",
				"zeroRecords": "No data available.",
				paginate: {
					next: '<i class="dashicons dashicons-arrow-right-alt2"></i>',
					previous: '<i class="dashicons dashicons-arrow-left-alt2"></i>'
				}
			},
			"fnDrawCallback": function (oSettings) {
				if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
					jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
				}
			},
			dom: 'Bfrtip',
			buttons: [{
				extend: 'excelHtml5',
				title: "",
				action: function (e, dt, node, config) {
					jQuery("#recent_visit_by_ip").parents(".panelcontent").find(".dataTables_processing").show();
					
					// Get current search parameters
					var formValues = getFormValues();
					
					// Make AJAX call for export with all data
					jQuery.ajax({
						url: ahc_ajax.ajax_url + '?action=recent_visitor_by_ip&page=all',
						type: 'GET',
						data: {
							from_date: formValues.from_date,
							to_date: formValues.to_date,
							ip: formValues.ip
						},
						success: function (res) {
							try {
								var response = typeof res === 'string' ? JSON.parse(res) : res;
								processExportData(response || []);
							} catch (error) {
								alert('Export failed. Please try again.');
								jQuery("#recent_visit_by_ip").parents(".panelcontent").find(".dataTables_processing").hide();
							}
						},
						error: function(xhr, status, error) {
							alert('Export failed. Please try again.');
							jQuery("#recent_visit_by_ip").parents(".panelcontent").find(".dataTables_processing").hide();
						}
					});
					
					function processExportData(data) {
						try {
							var columnsToExport = ["hit_ip_address", "ctr_name", "time", "duration", "day_hits"];
							var columnHeaders = ["IP Address", "Location", "Time", "Duration", "Day Hits"];
	
							var values = data.map(function (item) {
								return columnsToExport.map(function (column) {
									if (column === 'day_hits') {
										if (typeof item[column] === 'string' && item[column].includes('<button')) {
											var match = item[column].match(/>(\d+)</);
											return match ? match[1] : '0';
										}
										return item[column] || '0';
									}
									if (column === 'ctr_name') {
										if (typeof item[column] === 'string') {
											return item[column].replace(/<[^>]*>/g, '');
										}
										return item[column] || '';
									}
									if (column === 'duration') {
										if (typeof item[column] === 'string' && item[column].includes(':')) {
											return item[column];
										}
										if (typeof item[column] === 'number' || !isNaN(item[column])) {
											var totalSeconds = parseInt(item[column]) || 0;
											var hours = Math.floor(totalSeconds / 3600);
											var minutes = Math.floor((totalSeconds % 3600) / 60);
											var seconds = totalSeconds % 60;
											return String(hours).padStart(2, '0') + ':' + 
												   String(minutes).padStart(2, '0') + ':' + 
												   String(seconds).padStart(2, '0');
										}
										return item[column] || '00:00:01';
									}
									return item[column] || '';
								});
							});
	
							var workbook = XLSX.utils.book_new();
							var worksheet = XLSX.utils.aoa_to_sheet([columnHeaders].concat(values));
							
							worksheet['!cols'] = [
								{wch: 15}, // IP Address
								{wch: 30}, // Location
								{wch: 20}, // Time
								{wch: 12}, // Duration
								{wch: 10}  // Day Hits
							];
							
							XLSX.utils.book_append_sheet(workbook, worksheet, 'Recent Visitors');
							XLSX.writeFile(workbook, 'recent_visitors_by_ip.xlsx');
	
						} catch (error) {
							alert('Export failed. Please check console for details.');
						} finally {
							jQuery("#recent_visit_by_ip").parents(".panelcontent").find(".dataTables_processing").hide();
						}
					}
				}
			}]
		});
	
		// Function to reset form values (like pro version)
		function resetFormValues() {
			// Clear all form fields
			jQuery("#r_from_dt").val('');
			jQuery("#r_to_dt").val('');
			jQuery("#ip_addr").val('');
			
			// Clear datepicker if exists
			try {
				if (jQuery.fn.datepicker) {
					jQuery("#r_from_dt, #r_to_dt").datepicker('setDate', null);
				}
			} catch(e) {
				// Ignore datepicker errors
			}
		}
	
		// Use event delegation for form submission (like pro version)
		jQuery(document).on('submit', '.search_frm', function(e) {
			e.preventDefault();
			
			// Reload the DataTable with new parameters
			table.ajax.reload(null, false);
			
			return false;
		});
	
		// Handle submit button click directly (like pro version)
		jQuery(document).on('click', 'input[type="submit"]', function(e) {
			// Only handle buttons in forms related to our table
			if (jQuery(this).closest('.panel').find('#recent_visit_by_ip').length === 0) {
				return; // Not our button
			}
			
			e.preventDefault();
			
			// Reload the DataTable directly
			table.ajax.reload(null, false);
			
			return false;
		});
	
		// Handle clear button (like pro version)
		jQuery(document).on('click', '.clear_form', function(e) {
			// Only handle clear buttons related to our table
			if (jQuery(this).closest('.panel').find('#recent_visit_by_ip').length === 0) {
				return; // Not our button
			}
			
			e.preventDefault();
			
			resetFormValues();
			
			// Reload table with cleared filters
			table.ajax.reload(null, false);
			
			// Close the search panel
			jQuery('.search-panel').removeClass('open');
		});
	
		// Handle search panel toggle (like pro version)
		jQuery('#recent_visit_by_ip').closest('.panel').find('.search_data a').on('click', function(e) {
			e.preventDefault();
			var searchPanel = jQuery(this).closest('.panel').find('.search-panel');
			searchPanel.toggleClass('open');
		});
	
		// Modal functionality (keeping your existing modal code but simpler)
		jQuery(document).on('click', 'button[data-target="#DayHitsModal"]', function(e) {
			e.preventDefault();
			
			var button = jQuery(this);
			
			// Get all data from button attributes (already loaded from server)
			var ip = button.data('hitipaddress') || '';
			var time = button.data('time') || '';
			var hitdate = button.data('hitdate') || '';
			var duration = button.data('duration') || '00:00:01';
			var browser = button.data('browser') || 'Unknown Browser';
			var flag = button.data('ctr-code') || 'eg'; // FIXED: Use 'ctr-code' instead of 'flag'
			var location = button.data('hitcountry') || 'Unknown Location';
			
			// Parse location to get country and city
			var locationParts = location.split(', ');
			var country = locationParts[0] || 'Unknown Country';
			var city = locationParts[1] || 'Unknown City';
			
			// Build modal content with all data already available
			var headerHtml = `
			  <style>
				.modal-dialog {
				  max-width: 900px !important;
				  width: 85vw !important;
				  margin: 30px auto !important;
				}
				
				.visitor-header-enhanced {
				  display: flex; 
				  gap: 10px; 
				  align-items: center; 
				  margin-bottom: 20px; 
				  background: #f8fafc; 
				  border-radius: 10px; 
				  padding: 12px 16px; 
				  font-family: 'Segoe UI', 'Arial', sans-serif; 
				}
				
				.visitor-header-enhanced .city-country { 
				  font-weight: bold; 
				  color: #222; 
				}
				
				.visitor-header-enhanced .ip { 
				  color: #444; 
				  background: #f1f5f9; 
				  border-radius: 4px; 
				  padding: 2px 6px; 
				}
				
				.visitor-header-enhanced .duration { 
				  color: #2563eb; 
				  background: #e0e7ef; 
				  border-radius: 4px; 
				  padding: 2px 8px; 
				  font-weight: 500; 
				}
				
				.visitor-header-enhanced .device { 
				  background: #e0e7ef; 
				  color: #2563eb; 
				  border-radius: 16px; 
				  padding: 3px 10px; 
				  display: flex; 
				  align-items: center; 
				  gap: 4px; 
				}
				
				.visitor-image-container {
				  margin-top: 20px;
				  text-align: center;
				  border-radius: 8px;
				  overflow: hidden;
				}
				
				.visitor-image-container img {
				  width: 100%;
				  height: auto;
				  display: block;
				}
			  </style>
			  <div class="visitor-header-enhanced">
				<img class="flag" src="https://flagcdn.com/w40/${flag.toLowerCase()}.png" alt="flag" width="32" height="24" />
				<div class="city-country">${city}, ${country}</div>
				<div class="ip">IP: ${ip}</div>
				<div class="duration">Duration: ${duration}</div>
				<div class="device">
				  <i class="dashicons dashicons-desktop"></i> ${browser}
				</div>
			  </div>
			  <div class="visitor-image-container">
				<a target="_blank" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029">					
				  <img src="${ahc_ajax.plugin_url}images/Traffic_by_Monthly_IP_PRO.jpg" alt="Traffic by Monthly IP PRO" />
				</a>
			  </div>
			`;
			
			// Update modal content
			jQuery('#DayHitsModal .modal-body').html(headerHtml);
			jQuery('#DayHitsModal .modal-title').text('IP Tracking');
			
			// Show modal
			jQuery('#DayHitsModal').modal('show');
		});
		
		// Modal close functionality
		jQuery(document).on('click', '[data-dismiss="modal"]', function() {
			jQuery('#DayHitsModal').modal('hide');
		});
		
		// Reset modal content when closed
		jQuery('#DayHitsModal').on('hide.bs.modal', function (event) {
			jQuery('.modal-body').html('<div class="ahc_loader" style="width:100px !important; height:50px !important;">&nbsp;</div>');
		});
	}
	if (jQuery('#visit_time_graph_table').length) {
		visitTimeGraphTable();
	}

	function visitTimeGraphTable() {
		// Get current hour to calculate the starting page
		const currentHour = new Date().getHours();
		const pageLength = 10;
		const startingPage = Math.floor(currentHour / pageLength);

		const table = jQuery('#visit_time_graph_table').DataTable({
			"pageLength": pageLength,
			"searching": false,
			"ordering": false,
			"bLengthChange": false,
			"bFilter": true,
			"bInfo": false,
			"bAutoWidth": false,
			"bJQueryUI": true,
			"processing": true,
			"serverSide": true,
			"displayStart": startingPage * pageLength, // Start at the page containing current hour
			ajax: {
				url: ahc_ajax.ajax_url + '?action=visits_time_graph&fdt=' + jQuery("#vfrom_dt").val() + "&tdt=" + jQuery("#vto_dt").val(),
				data: function (d) {
					// Add current hour info to the request
					d.current_hour = currentHour;
					return d;
				}
			},
			dataSrc: 'data',
			columns: [
				{ data: 'time' },
				{ data: 'graph' },
				{ data: 'vtm_visitors' },
				{ data: 'vtm_visits' }
			],
			language: {
				processing: "<span class='loader'>&nbsp;</span>",
				"zeroRecords": "No data available.",
				paginate: {
					next: '<i class="dashicons dashicons-arrow-right-alt2"></i>',
					previous: '<i class="dashicons dashicons-arrow-left-alt2"></i>'
				}
			},
			"fnDrawCallback": function (oSettings) {
				if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
					jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
				}

				// Highlight current hour row after EVERY page draw/change
				highlightCurrentHourRow();
			},
			"fnInitComplete": function (oSettings) {
				setTimeout(function () {
					highlightCurrentHourRow();
				}, 500);
			},
			dom: 'Bfrtip',
			buttons: [{
				extend: 'excelHtml5',
				title: "",
				action: function (e, dt, node, config) {
					jQuery("#visit_time_graph_table").parents(".panelcontent").find(".dataTables_processing").show();
					jQuery.ajax({
						url: ahc_ajax.ajax_url + '?action=visits_time_graph&page=all&fdt=' + jQuery("#vfrom_dt").val() + "&tdt=" + jQuery("#vto_dt").val(),
						data: dt.ajax.params(),
						success: function (res, status, xhr) {
							var createXLSLFormatObj = [];

							/* XLS Head Columns */
							var xlsHeader = ["Time", "Visitors", "Visits", "Graph"];

							/* XLS Rows Data */
							var xlsRows = JSON.parse(res);

							createXLSLFormatObj.push(xlsHeader);
							jQuery.each(xlsRows, function (index, value) {
								var innerRowData = [];
								jQuery.each(value, function (ind, val) {
									innerRowData.push(val);
								});
								createXLSLFormatObj.push(innerRowData);
							});
							jQuery("#visit_time_graph_table").parents(".panelcontent").find(".dataTables_processing").hide();
							/* File Name */
							var filename = "visits_time_graph.xlsx";

							/* Sheet Name */
							var ws_name = "sheet1";

							if (typeof console !== 'undefined') console.log(new Date());
							var wb = XLSX.utils.book_new(),
								ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);

							/* Add worksheet to workbook */
							XLSX.utils.book_append_sheet(wb, ws, ws_name);

							/* Write workbook and Download */
							if (typeof console !== 'undefined') console.log(new Date());
							XLSX.writeFile(wb, filename);
							if (typeof console !== 'undefined') console.log(new Date());
						}
					})
				},
			}]
		});

		return table;
	}

	// Function to highlight current hour row with yellow background and left border
	// More efficient highlighting function
	function highlightCurrentHourRow() {
		const currentHour = new Date().getHours();
		const currentTimeString = (currentHour < 10 ? '0' + currentHour : currentHour) + ':00 - ' + (currentHour < 10 ? '0' + currentHour : currentHour) + ':59';

		// Remove previous highlighting first (more efficient than checking each row)
		jQuery('#visit_time_graph_table tbody tr').removeClass('current-hour-highlight');

		// Find and highlight current hour row
		jQuery('#visit_time_graph_table tbody tr').each(function () {
			const timeCell = jQuery(this).find('td:first-child').text().trim();
			if (timeCell === currentTimeString) {
				jQuery(this).addClass('current-hour-highlight');
				return false; // Break the loop once found
			}
		});
	}
	if (jQuery('#today_traffic_index_by_country').length) {
		trafficByIndexCountryTable();
	}
	function trafficByIndexCountryTable() {
		jQuery('#today_traffic_index_by_country').DataTable({
			"pageLength": 10,
			"searching": false,
			"ordering": false,
			"bLengthChange": false,
			"bFilter": true,
			"bInfo": false,
			"bAutoWidth": false,
			"bJQueryUI": true,
			"processing": true,
			"serverSide": true,
			ajax: ahc_ajax.ajax_url + '?action=today_traffic_index&fdt=' + jQuery("#t_from_dt").val() + "&tdt=" + jQuery("#t_to_dt").val(),
			dataSrc: 'data',
			columns: [
				{ data: 'no' },
				{ data: 'country' },
				{ data: 'ctr_name' },
				{ data: 'total' },
			],
			language: {
				processing: "<span class='loader'>&nbsp;</span>",
				"zeroRecords": "No data available.",
				paginate: {
					next: '<i class="dashicons dashicons-arrow-right-alt2"></i>',
					previous: '<i class="dashicons dashicons-arrow-left-alt2"></i>'
				}
			},
			"fnDrawCallback": function (oSettings) {
				if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
					jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
				}
			},
			dom: 'Bfrtip',
			buttons: [{
				extend: 'excelHtml5',
				title: "",
				action: function (e, dt, node, config) {
					jQuery("#today_traffic_index_by_country").parents(".panelcontent").find(".dataTables_processing").show();
					jQuery.ajax({
						url: ahc_ajax.ajax_url + '?action=today_traffic_index&page=all&fdt=' + jQuery("#t_from_dt").val() + "&tdt=" + jQuery("#t_to_dt").val(),
						data: dt.ajax.params(),
						success: function (res, status, xhr) {
							//console.log(res);

							var createXLSLFormatObj = [];

							/* XLS Head Columns */
							var xlsHeader = ["No", "Country", "Total"];

							/* XLS Rows Data */
							var xlsRows = JSON.parse(res);

							createXLSLFormatObj.push(xlsHeader);
							jQuery.each(xlsRows, function (index, value) {
								var innerRowData = [];
								jQuery.each(value, function (ind, val) {
									innerRowData.push(val);
								});
								createXLSLFormatObj.push(innerRowData);
							});
							jQuery("#today_traffic_index_by_country").parents(".panelcontent").find(".dataTables_processing").hide();

							/* File Name */
							var filename = "today_traffic_index.xlsx";

							/* Sheet Name */
							var ws_name = "sheet1";

							if (typeof console !== 'undefined') console.log(new Date());
							var wb = XLSX.utils.book_new(),
								ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);

							/* Add worksheet to workbook */
							XLSX.utils.book_append_sheet(wb, ws, ws_name);

							/* Write workbook and Download */
							if (typeof console !== 'undefined') console.log(new Date());
							XLSX.writeFile(wb, filename);
							if (typeof console !== 'undefined') console.log(new Date());

						}
					})
				},
				exportOptions: {
					columns: [0, 2, 3]
				},
			}]
		});

	}
	if (jQuery('#summary_statistics').find("tr").length > 1) {
		jQuery('#summary_statistics').DataTable({
			"pageLength": 100,
			"searching": false,
			"ordering": false,
			"bPaginate": false,
			"bLengthChange": false,
			"bFilter": true,
			"bInfo": false,
			"bAutoWidth": false,
			"fnDrawCallback": function (oSettings) {
				if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
					jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
				}
			},
			dom: 'Bfrtip',
			buttons: [{
				extend: 'excelHtml5',
				title: ""
			}]
		});

	}
	if (jQuery('#search_engine').find("tr").length > 1) {
		jQuery('#search_engine').DataTable({
			"pageLength": 100,
			"searching": false,
			"ordering": false,
			"bPaginate": false,
			"bLengthChange": false,
			"bFilter": true,
			"bInfo": false,
			"bAutoWidth": false,
			"fnDrawCallback": function (oSettings) {
				if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
					jQuery(oSettings.nTableWrapper).find('.dataTables_paginate').hide();
				}
			},
			dom: 'Bfrtip',
			buttons: [{
				extend: 'excelHtml5',
				title: ""
			}]
		});

	}

	jQuery(".export_data a").click(function (e) {
		e.preventDefault();
		jQuery(this).parents(".panel").find(".dt-buttons").find(".dt-button").trigger("click");

	})

	var dateFormat = "mm-dd-yy";
	if (jQuery("#from_dt").length && jQuery("#to_dt").length) {
		var from = jQuery("#from_dt").datepicker({
			defaultDate: 0,
			dateFormat: "mm-dd-yy",
			numberOfMonths: 1
		});


		var to = jQuery("#to_dt").datepicker({
			defaultDate: 0,
			dateFormat: "mm-dd-yy",
			numberOfMonths: 1
		});
	}


	if (jQuery("#summary_from_dt").length && jQuery("#summary_to_dt").length) {
		var from = jQuery("#summary_from_dt").datepicker({
			defaultDate: 0,
			dateFormat: "yy-mm-dd",
			numberOfMonths: 1
		});


		var to = jQuery("#summary_to_dt").datepicker({
			defaultDate: 0,
			dateFormat: "yy-mm-dd",
			numberOfMonths: 1
		});
	}


	jQuery("#to_dt").on("change", function () {
		from.datepicker("option", "maxDate", getDate(this));
	});

	jQuery("#from_dt").on("change", function () {
		to.datepicker("option", "minDate", getDate(this));
	});

	if (jQuery("#t_from_dt").length && jQuery("#t_to_dt").length) {
		var t_from_dt = jQuery("#t_from_dt").datepicker({
			defaultDate: 0,
			dateFormat: "mm-dd-yy",
			numberOfMonths: 1
		});


		var t_to_dt = jQuery("#t_to_dt").datepicker({
			defaultDate: 0,
			dateFormat: "mm-dd-yy",
			numberOfMonths: 1
		});
	}

	jQuery("#t_to_dt").on("change", function () {
		t_from_dt.datepicker("option", "maxDate", getDate(this));
	});

	jQuery("#t_from_dt").on("change", function () {
		t_to_dt.datepicker("option", "minDate", getDate(this));
	});

	if (jQuery("#vfrom_dt").length && jQuery("#vto_dt").length) {
		var vfrom = jQuery("#vfrom_dt").datepicker({
			defaultDate: 0,
			dateFormat: "mm-dd-yy",
			numberOfMonths: 1
		});

		var vto = jQuery("#vto_dt").datepicker({
			defaultDate: 0,
			dateFormat: "mm-dd-yy",
			numberOfMonths: 1
		});
	}

	jQuery("#vto_dt").on("change", function () {
		vfrom.datepicker("option", "maxDate", getDate(this));
	});

	jQuery("#vfrom_dt").on("change", function () {
		vto.datepicker("option", "minDate", getDate(this));
	});

	if (jQuery("#r_from_dt").length && jQuery("#r_to_dt").length) {
		var vfrom = jQuery("#r_from_dt").datepicker({
			defaultDate: 0,
			dateFormat: "mm-dd-yy",
			numberOfMonths: 1
		});

		var vto = jQuery("#r_to_dt").datepicker({
			defaultDate: 0,
			dateFormat: "mm-dd-yy",
			numberOfMonths: 1
		});
	}

	jQuery("#r_to_dt").on("change", function () {
		vfrom.datepicker("option", "maxDate", getDate(this));
	});

	jQuery("#r_from_dt").on("change", function () {
		vto.datepicker("option", "minDate", getDate(this));
	});


	function getDate(element) {
		var date;
		try {
			date = jQuery.datepicker.parseDate(dateFormat, element.value);
		} catch (error) {
			date = null;
		}
		return date;
	}
	jQuery(".search-panel .search_frm").submit(function (e) {
		e.preventDefault();
		var tableID = jQuery(this).parents(".panel").find(".panelcontent").find("table").attr("id");


		if (tableID == "recent_visit_by_ip") {
			jQuery('#' + tableID).DataTable().destroy();
			recentVisitorsOptimized();
			return false;
		}
		else if (tableID == "today_traffic_index_by_country") {
			jQuery('#' + tableID).DataTable().destroy();
			trafficByIndexCountryTable();
			return false;
		}
		else if (tableID == "lasest_search_words") {
			jQuery('#' + tableID).DataTable().destroy();
			latestSearchTable();
			return false;
		}
		else if (tableID == "visit_time_graph_table") {
			jQuery('#' + tableID).DataTable().destroy();
			visitTimeGraphTable();
			return false;
		}
		else
			return true;


	});
	jQuery(".clear_form").click(function (e) {
		jQuery(this).parents("form").find(".ahc_clear").val("");
		jQuery(this).parents("form").submit();
	});

	jQuery(".search_data a").click(function (e) {
		e.preventDefault();
		if (jQuery(this).parents(".panel").find(".search-panel").length)
			jQuery(this).parents(".panel").find(".search-panel").slideToggle();
		if (jQuery(this).parents(".panel").find(".dataTables_filter").length)
			jQuery(this).parents(".panel").find(".dataTables_filter").slideToggle();
	});
});
