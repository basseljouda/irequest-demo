<div class="manager-order-panel">
    <div class="manager-order-panel__header">
        <div class="manager-order-panel__title">
            <span class="manager-order-panel__id">#{{ $order->order_id }}</span>
            <span class="manager-order-panel__so">SO {{ $order->order_no ?? '—' }}</span>
            @if($order->consignment_order)
                <span class="manager-order-chip chip-consignment">Consignment</span>
            @endif
            <span class="manager-order-chip chip-status status-{{ $order->status }}">
                {{ ucwords(config('constant.orders.' . $order->status)) }}
            </span>
            @if($order->inservice_status)
                <span class="manager-order-chip chip-inservice">Inservice {{ strtoupper($order->inservice_status) }}</span>
            @endif
            @if($order->reassign_to)
                <span class="manager-order-chip chip-reassign">Reassigned ➜ #{{ $order->reassign_to }}</span>
            @endif
        </div>
        <div class="manager-order-header-actions">
            @if($primaryAction)
            <button class="btn btn-primary manager-order-action"
                    data-endpoint="{{ $primaryAction['endpoint'] ?? '' }}"
                    data-method="{{ $primaryAction['method'] ?? 'GET' }}"
                    {{ !empty($primaryAction['disabled']) ? 'disabled' : '' }}>
                <i class="fa {{ $primaryAction['icon'] ?? 'fa-check' }}"></i>
                {{ $primaryAction['label'] }}
            </button>
            @endif
            <button class="manager-order-close" type="button" aria-label="Close order drawer">
                &times;
            </button>
        </div>
    </div>

    <div class="manager-order-panel__body">
        <section class="manager-order-summary">
            <div class="summary-card">
                <span class="summary-label">Bill Start</span>
                <strong>{{ $order->bill_started ? dformat($order->bill_started, true) : '—' }}</strong>
                <span class="summary-meta">Taken by {{ optional($order->createdby)->name ?? 'System' }}</span>
            </div>
            <div class="summary-card">
                <span class="summary-label">Bill End</span>
                <strong>{{ $order->bill_completed ? dformat($order->bill_completed, true) : 'In progress' }}</strong>
                <span class="summary-meta">Duration {{ $totalDays }} day(s)</span>
            </div>
            <div class="summary-card">
                <span class="summary-label">Order Total</span>
                <strong>${{ number_format($orderTotal, 2) }}</strong>
                <span class="summary-meta">{{ count($order->equipments) }} equipment combo(s)</span>
            </div>
            <div class="summary-card">
                <span class="summary-label">Order Goal</span>
                <strong>{{ $orderGoal ? dformat($orderGoal, true) : '—' }}</strong>
                <span class="summary-meta">{{ $deliveryDiff ?? 'Awaiting delivery' }}</span>
            </div>
        </section>

        <div class="manager-order-layout">
            <main class="manager-order-main">
                <section class="manager-order-card tabs-card">
                    <div class="manager-order-tabs">
                        <button class="tab-link active" data-target="#manager-order-summary">Summary</button>
                        <button class="tab-link" data-target="#manager-order-attachments">Attachments</button>
                        <button class="tab-link" data-target="#manager-order-log">Activity Log</button>
                    </div>
                    <div class="tab-panel active" id="manager-order-summary">
                        <div class="manager-order-table-wrapper">
                            <table class="manager-order-table">
                                <thead>
                                <tr>
                                    <th>Equipment Combo</th>
                                    <th>Inventory</th>
                                    <th>Assets</th>
                                    @permission('view_amount')
                                        <th class="text-right">Rate</th>
                                    @endpermission
                                    <th class="text-right">Days</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->equipments as $entry)
                                    @php
                                        $assets = $order->viewAssets($entry->id);
                                        $removedAssets = is_array($entry->removed_assets) ? $entry->removed_assets : [];
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ optional($entry->equipment)->name ?? '—' }}</div>
                                        </td>
                                        <td>
                                            {{ optional($entry->inventory)->title ?? 'NA' }}
                                        </td>
                                        <td>
                                            @if($assets->count())
                                                <div class="manager-order-asset-list">
                                                    @foreach($assets as $asset)
                                                        <span class="asset-pill {{ in_array($asset->id, $removedAssets) ? 'asset-pill--removed' : '' }}">
                                                            {{ $asset->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">No assets assigned</span>
                                            @endif
                                        </td>
                                        @permission('view_amount')
                                            <td class="text-right">
                                                ${{ number_format($entry->price_day, 2) }}
                                            </td>
                                        @endpermission
                                        <td class="text-right">{{ $entry->rental_days ?? '—' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-panel" id="manager-order-attachments">
                        @if($attachments->count())
                            <div class="manager-order-attachments">
                                @foreach($attachments as $file)
                                    <div class="attachment-row">
                                        <div class="attachment-icon"><i class="fa fa-file-alt"></i></div>
                                        <div class="attachment-info">
                                            <a href="{{ asset('user-uploads/orders/'.$file->filename) }}" target="_blank">
                                                {{ $file->original_name ?? $file->filename }}
                                            </a>
                                            <span class="attachment-meta">
                                                {{ dformat($file->created_at, true) }} · {{ optional($file->user)->name ?? 'Unknown' }}
                                            </span>
                                        </div>
                                        <div class="attachment-actions">
                                            <a href="{{ asset('user-uploads/orders/'.$file->filename) }}" download>
                                                <i class="fa fa-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="manager-order-placeholder">
                                <i class="fa fa-paperclip"></i>
                                <span>No files attached yet.</span>
                            </div>
                        @endif
                    </div>
                    <div class="tab-panel" id="manager-order-log">
                        @if($statusLog->count())
                            <div class="manager-order-log">
                                @foreach($statusLog as $entry)
                                    <div class="log-entry">
                                        <div class="log-timestamp">{{ dformat($entry->created_at, true) }}</div>
                                        <div class="log-body">
                                            <div class="log-title">{{ $entry->status }}</div>
                                            <div class="log-meta">
                                                {{ optional($entry->user)->name ?? 'System' }}
                                                @if($entry->notes)
                                                    · <span class="text-muted">{{ $entry->notes }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="manager-order-placeholder">
                                <i class="fa fa-history"></i>
                                <span>No status transitions recorded yet.</span>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="manager-order-card">
                    <header class="manager-order-card__header">
                        <div>
                            <h4>Timeline</h4>
                            <p class="muted">Date Needed: {{ $order->date_needed ? dformat($order->date_needed, true) : '—' }}</p>
                        </div>
                        <p class="manager-order-card__meta">Key milestones and supporting documentation</p>
                    </header>

                    <div class="manager-order-timeline">
                        @foreach($timeline as $item)
                            <div class="manager-order-timeline__item">
                                <div class="timeline-icon">{{ $item['icon'] }}</div>
                                <div class="timeline-body">
                                    <div class="timeline-title">{{ $item['title'] }}</div>
                                    <div class="timeline-timestamp">{{ $item['timestamp'] }}</div>
                                    @if(!empty($item['meta']))
                                        <div class="timeline-meta">{{ $item['meta'] }}</div>
                                    @endif
                                    @if(!empty($item['links']))
                                        <div class="timeline-links">
                                            <a href="{{ $item['links']['web'] }}" target="_blank">Web</a>
                                            <span>·</span>
                                            <a href="{{ $item['links']['pdf'] }}" target="_blank">PDF</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="manager-order-card">
                    <header class="manager-order-card__header">
                        <div>
                            <h4>Notifications</h4>
                            <p>Choose team members to keep in the loop</p>
                        </div>
                    </header>
                    <div class="manager-order-notify-placeholder">
                        <i class="fa fa-user-plus"></i>
                        <span>Select recipients (coming soon)</span>
                    </div>
                </section>
            </main>

            <aside class="manager-order-aside">
                <section class="manager-order-card sticky">
                    <header class="manager-order-card__header">
                        <div>
                            <h4>Site &amp; Patient Details</h4>
                            <p>Snapshot for quick decisions</p>
                        </div>
                    </header>
                    <div class="manager-order-detail-block">
                        <span class="detail-label">Facility</span>
                        <strong>{{ optional($order->hospital)->name ?? '—' }}</strong>
                        <span class="detail-meta">{{ optional($order->hospital)->address ?? '' }}</span>
                        <span class="detail-meta">Cost Center: {{ optional($order->costcenter)->name ?? '—' }}</span>
                    </div>
                    <div class="manager-order-detail-block">
                        <span class="detail-label">Patient</span>
                        <strong>{{ $order->patient_name ?? '—' }}</strong>
                        <div class="detail-inline">
                            <span>Unit/Floor: <strong>{{ $order->unit_floor ?? '—' }}</strong></span>
                            <span>Room: <strong>{{ $order->room_no ?? '—' }}</strong></span>
                        </div>
                        <a href="{{ route('orders.edit', $order->id) }}" class="detail-action" target="_blank">
                            Change patient information
                        </a>
                    </div>
                    <div class="manager-order-detail-block">
                        <span class="detail-label">Primary Contact</span>
                        <strong>{{ $order->staff ? $order->staff->fullname() : '—' }}</strong>
                        <span class="detail-meta">{{ optional($order->staff)->email }}</span>
                        <span class="detail-meta">{{ optional($order->staff)->phone }}</span>
                    </div>
                    <div class="manager-order-detail-block">
                        <span class="detail-label">Delivery Contact</span>
                        <strong>{{ optional($order->hospital)->contact_name ?? '—' }}</strong>
                        <span class="detail-meta">{{ $order->contact_phone ?? optional($order->hospital)->contact_phone }}</span>
                    </div>
                    <div class="manager-order-detail-block">
                        <span class="detail-label">Links</span>
                        <div class="detail-links">
                            <a href="{{ route('orders.showPDF', [$order->id, 'note' => 'noteaccept']) }}" target="_blank">Acceptance Form</a>
                            <a href="{{ route('orders.showPDF', [$order->id, 'note' => 'notepickup']) }}" target="_blank">Pickup Form</a>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>

<script>
    (function () {
        const drawer = document.getElementById('manager-order-drawer');
        if (!drawer) { return; }

        drawer.querySelectorAll('.tab-link').forEach(function (tab) {
            tab.addEventListener('click', function () {
                drawer.querySelectorAll('.tab-link').forEach(function (t) { t.classList.remove('active'); });
                drawer.querySelectorAll('.tab-panel').forEach(function (panel) { panel.classList.remove('active'); });
                tab.classList.add('active');
                drawer.querySelector(tab.dataset.target).classList.add('active');
            });
        });
    })();
</script>

