<div id="modalBody" class="modal-body order_tracking-container p-4" style="zoom:90%">
    <!-- Internal Notes Timeline -->
   
    
    <div class="timeline-container">
        @forelse ($order->statustrans as $trans)
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <span class="badge badge-info mb-2">{{ $trans->user->name ?? 'Unknown User' }}</span>
                        <div class="d-flex align-items-center mt-2">
                            <span class="avatar-sm bg-primary text-white mr-2">
                               
                            </span>
                            <strong>{!! ucfirst($trans->status) !!}</strong>
                        </div>
                    </div>
                    <div class="text-right text-nowrap ml-3">
                        <small class="text-muted d-block">
                            {{ \Carbon\Carbon::parse($trans->created_at)->format('M d, Y') }}
                        </small>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($trans->created_at)->format('h:i A') }}
                        </small>
                    </div>
                </div>
                <p class="text-info mb-0 ml-3">
                    {{ $trans->notes ?? '—' }}
                </p>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="fa fa-inbox fa-3x mb-3 d-block" style="opacity: 0.25;"></i>
            <p class="mb-0">No tracking history available</p>
        </div>
        @endforelse
    </div>
    
    <!-- Customer Notes Section -->
    <div class="customer-notes-box mb-4">
        <h6 class="font-weight-bold mb-2">
            <i class="fa fa-comment-dots text-primary mr-2"></i>@trans(Customer) @trans(Notes)
        </h6>
        <p class="text-muted mb-0">
            {!! $order->notes ?? '<em class="text-muted">No customer notes available</em>' !!}
        </p>
    </div>
</div>

<style>
    /* Customer Notes Box */
    .customer-notes-box {
        background: linear-gradient(to right, #f8f9fa, #ffffff);
        border-left: 4px solid #667eea;
        padding: 20px;
        border-radius: 8px;
    }

    /* Timeline Container */
    .timeline-container {
        position: relative;
        padding-left: 10px;
    }

    /* Timeline Item */
    .timeline-item {
        position: relative;
        padding-left: 40px;
        padding-bottom: 30px;
        border-left: 2px solid #e0e0e0;
    }

    .timeline-item:last-child {
        border-left: 2px solid transparent;
        padding-bottom: 0;
    }

    /* Timeline Dot */
    .timeline-dot {
        position: absolute;
        left: -9px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #667eea;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #667eea;
        z-index: 1;
    }

    /* Timeline Content */
    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 3px solid #667eea;
        transition: all 0.3s ease;
    }

    .timeline-content:hover {
        background: #e9ecef;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* Avatar Circle */
    .avatar-sm {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
    }

    /* Badge Styling */
    .badge-success {
        background-color: #28a745;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
    }

    /* Responsive Adjustments */
    @media (max-width: 576px) {
        .timeline-item {
            padding-left: 30px;
        }
        
        .timeline-dot {
            left: -8px;
            width: 14px;
            height: 14px;
        }
        
        .text-nowrap {
            white-space: normal !important;
        }
    }

    /* Modal Body Padding Override */
    .order_tracking-container {
        max-height: 600px;
        overflow-y: auto;
    }

    /* Custom Scrollbar */
    .order_tracking-container::-webkit-scrollbar {
        width: 8px;
    }

    .order_tracking-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .order_tracking-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .order_tracking-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>