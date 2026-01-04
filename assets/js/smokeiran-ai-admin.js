/**
 * Smokeiran AI Admin JavaScript
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Process single queue item
        $('#smokeiran-process-queue').on('click', function() {
            processQueue(1);
        });

        // Process batch
        $('#smokeiran-process-batch').on('click', function() {
            var batchSize = $(this).data('batch') || 5;
            processQueue(batchSize);
        });

        // Refresh queue
        $('#smokeiran-refresh-queue').on('click', function() {
            location.reload();
        });

        // Delete queue item
        $(document).on('click', '.smokeiran-delete-item', function() {
            if (!confirm(smokeiranAI.strings.confirm_delete)) {
                return;
            }

            var $button = $(this);
            var itemId = $button.data('item-id');
            var $row = $button.closest('tr');

            $button.prop('disabled', true);

            $.ajax({
                url: smokeiranAI.ajax_url,
                type: 'POST',
                data: {
                    action: 'smokeiran_delete_queue_item',
                    nonce: smokeiranAI.nonce,
                    id: itemId
                },
                success: function(response) {
                    if (response.success) {
                        $row.fadeOut(300, function() {
                            $(this).remove();
                            updateQueueStats();
                        });
                    } else {
                        alert(response.data.message);
                        $button.prop('disabled', false);
                    }
                },
                error: function() {
                    alert(smokeiranAI.strings.error);
                    $button.prop('disabled', false);
                }
            });
        });

        // Process queue function
        function processQueue(batchSize) {
            var $message = $('#smokeiran-queue-message');
            $message.html('<p>' + smokeiranAI.strings.generating + '</p>')
                    .removeClass('error success')
                    .addClass('notice notice-info');

            $('#smokeiran-process-queue, #smokeiran-process-batch').prop('disabled', true);

            $.ajax({
                url: smokeiranAI.ajax_url,
                type: 'POST',
                data: {
                    action: 'smokeiran_process_queue',
                    nonce: smokeiranAI.nonce,
                    batch_size: batchSize
                },
                success: function(response) {
                    $('#smokeiran-process-queue, #smokeiran-process-batch').prop('disabled', false);

                    if (response.success) {
                        var message = 'Processed ' + response.data.processed + ' item(s).';
                        
                        if (response.data.errors.length > 0) {
                            message += ' Errors: ' + response.data.errors.join(', ');
                            $message.html('<p>' + message + '</p>')
                                    .removeClass('notice-info')
                                    .addClass('notice notice-warning');
                        } else {
                            $message.html('<p>' + message + '</p>')
                                    .removeClass('notice-info')
                                    .addClass('notice notice-success');
                        }

                        // Refresh page after 2 seconds
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $message.html('<p>' + response.data.message + '</p>')
                                .removeClass('notice-info')
                                .addClass('notice notice-error');
                    }
                },
                error: function() {
                    $('#smokeiran-process-queue, #smokeiran-process-batch').prop('disabled', false);
                    $message.html('<p>' + smokeiranAI.strings.error + '</p>')
                            .removeClass('notice-info')
                            .addClass('notice notice-error');
                }
            });
        }

        // Update queue stats
        function updateQueueStats() {
            $.ajax({
                url: smokeiranAI.ajax_url,
                type: 'POST',
                data: {
                    action: 'smokeiran_get_queue_status',
                    nonce: smokeiranAI.nonce
                },
                success: function(response) {
                    if (response.success && response.data.stats) {
                        var stats = response.data.stats;
                        $('.stat-box').each(function() {
                            var $this = $(this);
                            var label = $this.find('.stat-label').text().toLowerCase();
                            
                            if (stats[label] !== undefined) {
                                $this.find('.stat-value').text(stats[label]);
                            }
                        });
                    }
                }
            });
        }

        // Auto-refresh queue stats every 30 seconds
        if ($('.smokeiran-ai-queue').length) {
            setInterval(updateQueueStats, 30000);
        }
    });

})(jQuery);
