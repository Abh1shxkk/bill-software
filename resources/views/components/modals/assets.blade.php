{{--
    Modal Components Assets Include
    
    Include this partial in any Blade template to load the Item and Batch
    modal component assets (CSS and JavaScript).
    
    Usage:
    @include('components.modals.assets')
    
    Then use the modal components:
    <x-modals.item-selection id="chooseItemsModal" module="sale" />
    <x-modals.batch-selection id="batchSelectionModal" module="sale" />
--}}

<!-- Modal Component CSS -->
<link rel="stylesheet" href="{{ asset('css/components/modal-components.css') }}">

<!-- Modal Component JavaScript -->
<script src="{{ asset('js/components/item-modal.js') }}"></script>
<script src="{{ asset('js/components/batch-modal.js') }}"></script>
