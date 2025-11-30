<x-app-layout>
    @section('title')
        Complain Activity Log
    @endsection

    {{-- Include Complaint Table Styles Template --}}
    @include('components.complaint-table-styles')

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Complain Activity Log</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Requisition Slip form
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Complain Log</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Enhanced Table Section -->
    <div class="row">
        <div class="col-12">
            <!-- Action Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <!-- Optional: Add filter buttons or other controls here -->
                </div>
                <div>
                    <button class="btn btn-info" type="button" onclick="refreshTable()">
                        <i class="ph-bold ph-arrows-clockwise"></i>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>

            <!-- Enhanced Table Container -->
            <div class="main-table-container">
                <!-- Table Header -->
                <div class="table-header-enhanced">
                    <h4 class="table-title">
                        <i class="ph-duotone ph-clock-clockwise"></i>
                        Activity Logs
                    </h4>
                    <p class="table-subtitle">
                        A read-only log of all activities related to Complain Requisitions.
                    </p>
                </div>

                <!-- Table Content -->
                <div class="table-responsive">
                    <table class="w-100 display" id="complainLogTable">
                        <thead>
                            <tr>
                                <th><i class="ph-duotone ph-user me-1"></i>User</th>
                                <th><i class="ph-duotone ph-chat-text me-1"></i>Description</th>
                                <th><i class="ph-duotone ph-file-text me-1"></i>event</th>
                                <th><i class="ph-duotone ph-globe me-1"></i>Requisition</th>
                                <th class="text-center"><i class="ph-duotone ph-calendar me-1"></i>Date</th>
                                <th><i class="ph-duotone ph-gear me-1"></i>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="activityDetailModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-enhanced">
                    <h5 class="modal-title modal-title-enhanced" id="activityDetailModalLabel">
                        <i class="ph-duotone ph-info"></i>
                        Activity Detail
                    </h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-enhanced">
                    <!-- Activity Details will be populated here -->
                    <div class="row">
                        <div class="col-12">
                            <div class="detail-section">
                                <div class="section-header">
                                    <i class="ph-duotone ph-info-circle"></i>
                                    Activity Information
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-card">
                                            <div class="info-row">
                                                <div class="info-label">
                                                    <i class="ph-duotone ph-user text-primary"></i>
                                                    User:
                                                </div>
                                                <div class="info-value readonly" id="detail_user_name"></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">
                                                    <i class="ph-duotone ph-calendar text-info"></i>
                                                    Date & Time:
                                                </div>
                                                <div class="info-value readonly" id="detail_created_at"></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">
                                                    <i class="ph-duotone ph-globe text-success"></i>
                                                    IP Address:
                                                </div>
                                                <div class="info-value readonly" id="detail_ip_address"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-card">
                                            <div class="info-row">
                                                <div class="info-label">
                                                    <i class="ph-duotone ph-tag text-warning"></i>
                                                    Log Name:
                                                </div>
                                                <div class="info-value readonly" id="detail_log_name"></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">
                                                    <i class="ph-duotone ph-lightning text-danger"></i>
                                                    Event:
                                                </div>
                                                <div class="info-value readonly" id="detail_event"></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">
                                                    <i class="ph-duotone ph-file-text text-secondary"></i>
                                                    Subject:
                                                </div>
                                                <div class="info-value readonly" id="detail_subject"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="detail-section">
                                <div class="section-header">
                                    <i class="ph-duotone ph-chat-text"></i>
                                    Description
                                </div>
                                <div class="objectives-container">
                                    <div class="objectives-text" id="detail_description">
                                        <!-- Description will be populated here -->
                                    </div>
                                </div>
                            </div>

                            <div class="detail-section">
                                <div class="section-header">
                                    <i class="ph-duotone ph-device-mobile"></i>
                                    User Agent Information
                                </div>
                                <div class="user-agent-container">
                                    <div class="user-agent-text" id="detail_user_agent">
                                        <!-- User agent will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-bs-dismiss="modal" type="button">
                        <i class="ph-duotone ph-x me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // === SweetAlert2 Reusable Functions ===
        function successMessage(message, title = 'Success', timer = 1500) {
            Swal.fire({
                icon: 'success',
                title: title,
                text: message,
                timer: timer,
                showConfirmButton: false
            });
        }

        function errorMessage(message, title = 'Error') {
            Swal.fire({
                icon: 'error',
                title: title,
                text: message
            });
        }

        function formatDateTime(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '-';

            return date.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }

        function truncateText(text, maxLength = 100) {
            if (!text) return '-';
            if (text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        }

        function refreshTable() {
            $('#complainLogTable').DataTable().ajax.reload(null, false);
            successMessage('Table refreshed successfully!', 'Refreshed', 1000);
        }

        $(document).ready(function () {
            // === DataTable ===
            let table = $('#complainLogTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('complain.log.data') }}",
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [
                    {
                        data: 'causer',
                        name: 'causer',
                        width: '15%',
                        render: function (data, type, row) {
                            if (data && data.name) {
                                return `<span class="fw-medium">${data.name}</span>`;
                            }
                            return '<span class="text-muted">System</span>';
                        }
                    },
                    {
                        data: 'description',
                        name: 'description',
                        width: '20%',
                        render: function (data, type, row) {
                            return truncateText(data, 90);
                        }
                    },
                    {
                        data: 'event',
                        name: 'event',
                        width: '10%',
                        render: function (data, type, row) {
                            if (data) {
                                return `<span class="badge bg-info">${data || 'Unknown'}</span>`;
                            }
                            return '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'properties',
                        name: 'requisition_no',
                        width: '9%',
                        render: function (data, type, row) {
                            if (data && data.requisition_no) {
                                return `<code class="small"><strong>${data.requisition_no}</strong></code>`;
                            }
                            return '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        width: '15%',
                        render: function (data, type, row) {
                            return formatDateTime(data);
                        }
                    },
                    {
                        data: 'id',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '8%',
                        render: function (data, type, row) {
                            return `
                                <div class="action-btn-group">
                                    <button type="button" class="btn btn-info btn-sm detail-button action-btn-hover"
                                            data-activity='${JSON.stringify(row)}'
                                            data-tooltip="View Details">
                                        <i class="ph-duotone ph-eye"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[5, 'desc']],
                pageLength: 25,
                responsive: true
            });

            let searchInput = $('#complainLogTable_filter input');
            searchInput.unbind();
            let debounceTimer;
            searchInput.bind('keyup', function (e) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    let searchTerm = searchInput.val();
                    table.search(searchTerm).draw();
                }, 500);
            });

            // Detail button click handler
            $('#complainLogTable tbody').on('click', '.detail-button', function () {
                try {
                    const activityData = $(this).data('activity');

                    // Populate modal with activity data
                    $('#detail_user_name').text(activityData.causer ? activityData.causer.name : 'System');
                    $('#detail_created_at').text(formatDateTime(activityData.created_at));
                    $('#detail_log_name').text(activityData.log_name || '-');
                    $('#detail_event').text(activityData.event || '-');
                    $('#detail_description').text(activityData.description || '-');

                    // Handle IP address
                    if (activityData.properties && activityData.properties.ip) {
                        $('#detail_ip_address').text(activityData.properties.ip);
                    } else {
                        $('#detail_ip_address').text('-');
                    }

                    // Handle User Agent
                    if (activityData.properties && activityData.properties.user_agent) {
                        $('#detail_user_agent').text(activityData.properties.user_agent);
                    } else {
                        $('#detail_user_agent').text('-');
                    }

                    // Handle Subject
                    if (activityData.subject && activityData.subject.id) {
                        $('#detail_subject').text(`${activityData.subject_type || 'Unknown'} #${activityData.subject.id}`);
                    } else {
                        $('#detail_subject').text('-');
                    }

                    // Handle Properties
                    if (activityData.properties && Object.keys(activityData.properties).length > 0) {
                        // Remove sensitive info and format nicely
                        const filteredProperties = { ...activityData.properties };
                        delete filteredProperties.user_agent; // Already shown separately
                        delete filteredProperties.ip; // Already shown separately

                        if (Object.keys(filteredProperties).length > 0) {
                            $('#detail_properties').text(JSON.stringify(filteredProperties, null, 2));
                            $('#propertiesSection').show();
                        } else {
                            $('#propertiesSection').hide();
                        }
                    } else {
                        $('#propertiesSection').hide();
                    }

                    $('#activityDetailModal').modal('show');

                } catch (error) {
                    console.error('Error parsing activity data:', error);
                    errorMessage('Error loading activity details');
                }
            });

            // Custom Tooltip Handler for Action Buttons
            function initActionTooltips() {
                $(document).off('mouseenter.customTooltip mouseleave.customTooltip', '.action-btn-hover');

                $(document).on('mouseenter.customTooltip', '.action-btn-hover', function (e) {
                    const tooltipText = $(this).attr('data-tooltip');
                    if (tooltipText && !$(this).data('tooltip-element')) {
                        const tooltip = $('<div class="action-tooltip">' + tooltipText + '</div>');
                        $('body').append(tooltip);

                        const button = $(this);
                        let isDestroyed = false;

                        function updateTooltipPosition() {
                            if (isDestroyed || !button.is(':visible') || !tooltip.parent().length) {
                                return;
                            }

                            const buttonOffset = button.offset();
                            if (!buttonOffset) return;

                            const buttonWidth = button.outerWidth();
                            const buttonHeight = button.outerHeight();
                            const tooltipWidth = tooltip.outerWidth();
                            const tooltipHeight = tooltip.outerHeight();
                            const windowWidth = $(window).width();
                            const scrollTop = $(window).scrollTop();

                            let left = buttonOffset.left + (buttonWidth / 2) - (tooltipWidth / 2);
                            let top = buttonOffset.top - tooltipHeight - 12;

                            if (left < 10) {
                                left = 10;
                            } else if (left + tooltipWidth > windowWidth - 10) {
                                left = windowWidth - tooltipWidth - 10;
                            }

                            if (top < scrollTop + 10) {
                                top = buttonOffset.top + buttonHeight + 12;
                                tooltip.addClass('below');
                            } else {
                                tooltip.removeClass('below');
                            }

                            tooltip.css({
                                position: 'absolute',
                                left: left + 'px',
                                top: top + 'px',
                                zIndex: 9999
                            });
                        }

                        setTimeout(() => {
                            updateTooltipPosition();
                        }, 10);

                        setTimeout(() => {
                            if (!isDestroyed) {
                                tooltip.addClass('show');
                            }
                        }, 100);

                        button.data('tooltip-element', tooltip);

                        const tooltipId = 'tooltip_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                        button.data('tooltip-id', tooltipId);

                        let scrollTimeout;
                        function throttledUpdate() {
                            if (scrollTimeout) {
                                clearTimeout(scrollTimeout);
                            }
                            scrollTimeout = setTimeout(() => {
                                if (!isDestroyed) {
                                    updateTooltipPosition();
                                }
                            }, 10);
                        }

                        $(window).on('scroll.' + tooltipId + ' resize.' + tooltipId, throttledUpdate);

                        button.data('tooltip-cleanup', function () {
                            isDestroyed = true;
                            $(window).off('.' + tooltipId);
                            if (scrollTimeout) {
                                clearTimeout(scrollTimeout);
                            }
                        });
                    }
                });

                $(document).on('mouseleave.customTooltip', '.action-btn-hover', function (e) {
                    const button = $(this);
                    const tooltip = button.data('tooltip-element');
                    const cleanup = button.data('tooltip-cleanup');

                    if (tooltip) {
                        tooltip.removeClass('show');
                        setTimeout(() => {
                            tooltip.remove();
                        }, 200);

                        if (cleanup) {
                            cleanup();
                        }

                        button.removeData('tooltip-element');
                        button.removeData('tooltip-id');
                        button.removeData('tooltip-cleanup');
                    }
                });
            }

            // Initialize tooltips after DataTable is ready
            table.on('draw', function () {
                initActionTooltips();
            });

            initActionTooltips();

            // Enhanced search placeholder
            $('#complainLogTable_filter input').attr({
                'placeholder': 'Search activity logs...',
                'class': 'form-control'
            });

            // Add fade-in animation to DataTable wrapper
            $('.dataTables_wrapper').css({
                'animation': 'fadeInUp 0.8s ease-out forwards',
                'opacity': '0'
            });

            setTimeout(function () {
                $('.dataTables_wrapper').css('opacity', '1');
            }, 200);
        });
    </script>
    @endpush
</x-app-layout>
