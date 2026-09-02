<style>
    .crm-board {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding: 1rem;
        margin-inline: -.25rem;
        min-height: calc(100vh - 230px);
        background: var(--phoenix-kanban-bg, #e3e6ed);
        border-radius: 1rem;
    }
    .crm-board-col {
        min-width: 272px;
        width: 272px;
        flex: 0 0 272px;
        background: var(--phoenix-kanban-column-bg, var(--phoenix-body-bg));
        border-radius: .75rem;
        display: flex;
        flex-direction: column;
        box-shadow: 0 .125rem .25rem rgba(23, 34, 66, .04);
    }
    .crm-board-head { padding: .15rem 1rem 0; }
    .crm-board-head-inner {
        display: flex;
        align-items: center;
        gap: .5rem;
        border-bottom-width: 3px;
        border-bottom-style: solid;
        padding: .9rem 0 .75rem;
    }
    .crm-board-title { font-size: .95rem; font-weight: 700; margin: 0; }
    .crm-board-count {
        background: var(--phoenix-gray-200, #e3e6ed);
        color: var(--phoenix-body-color);
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 700;
        min-width: 1.35rem;
        height: 1.35rem;
        padding: 0 .4rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .crm-board-body {
        flex: 1;
        overflow-y: auto;
        max-height: calc(100vh - 280px);
        padding: .65rem .7rem .85rem;
    }
    .crm-ticket {
        background: var(--phoenix-emphasis-bg, #fff);
        border: 1px solid var(--phoenix-border-color, #e3e6ed);
        border-radius: .5rem;
        padding: .85rem;
        margin-bottom: .55rem;
        transition: box-shadow .15s ease, transform .15s ease;
    }
    .crm-ticket:hover { box-shadow: 0 .5rem 1.25rem rgba(23, 34, 66, .08); transform: translateY(-1px); }
    .crm-ticket-name { font-weight: 700; color: inherit; text-decoration: none; line-height: 1.3; }
    .crm-ticket-name:hover { color: var(--phoenix-primary, #3874ff); }
    .crm-empty {
        border: 1px dashed var(--phoenix-border-color, #d8dee9);
        border-radius: .75rem;
        padding: 1.5rem .75rem;
        text-align: center;
        color: var(--phoenix-secondary-color, #8a94ad);
        font-size: .85rem;
    }
    .crm-stat { border: 0; border-radius: 1rem; height: 100%; }
    .crm-stat h3 { font-weight: 800; letter-spacing: -.03em; margin: 0; }
    .crm-stat-icon {
        width: 2.75rem; height: 2.75rem; border-radius: .85rem;
        display: inline-flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.55);
    }
    .crm-timeline { position: relative; padding-inline-start: 1.5rem; }
    .crm-timeline::before {
        content: ""; position: absolute; inset-inline-start: 7px; top: 8px; bottom: 8px;
        width: 2px; background: var(--phoenix-border-color, #e3e6ed);
    }
    .crm-timeline-item { position: relative; padding-bottom: 1.25rem; }
    .crm-timeline-dot {
        position: absolute; inset-inline-start: -1.5rem; top: 2px;
        width: 16px; height: 16px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff; box-shadow: 0 0 0 3px var(--phoenix-emphasis-bg, #fff);
    }
    .crm-timeline-dot svg, .crm-timeline-dot [data-feather] { width: 10px; height: 10px; }
    .crm-action-icon {
        width: 32px; height: 32px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid var(--phoenix-border-color, #e3e6ed);
        color: inherit; text-decoration: none;
    }
    .crm-action-icon:hover { background: var(--phoenix-gray-100, #f5f7fa); }
    .crm-table thead th {
        font-size: .72rem; text-transform: uppercase; letter-spacing: .04em;
        color: var(--phoenix-secondary-color, #6e7891); font-weight: 700;
        border-bottom-width: 1px; white-space: nowrap;
    }
    .crm-table tbody td { vertical-align: middle; }
    .bg-gradient-primary-soft { background: linear-gradient(135deg, rgba(56, 116, 255, .14) 0%, rgba(56, 116, 255, .04) 100%); }
    .bg-gradient-info-soft { background: linear-gradient(135deg, rgba(0, 151, 194, .14) 0%, rgba(0, 151, 194, .04) 100%); }
    .bg-gradient-success-soft { background: linear-gradient(135deg, rgba(37, 176, 3, .14) 0%, rgba(37, 176, 3, .04) 100%); }
    .bg-gradient-warning-soft { background: linear-gradient(135deg, rgba(229, 120, 11, .14) 0%, rgba(229, 120, 11, .04) 100%); }
    [data-bs-theme="dark"] .crm-stat-icon { background: rgba(255,255,255,.08); }
    [data-bs-theme="dark"] .crm-ticket { background: var(--phoenix-emphasis-bg); }
</style>
