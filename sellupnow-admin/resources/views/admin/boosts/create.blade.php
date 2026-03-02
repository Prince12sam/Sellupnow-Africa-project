@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><h3>Create Boost</h3></div>
    <div class="card-body">
        <form method="post" action="{{ route('admin.boosts.store') }}">
            @csrf
            <div class="mb-3">
                <label>Listing</label>
                <input id="listing_search" class="form-control" placeholder="Search listing by title..." autocomplete="off" />
                <input id="listing_id" name="listing_id" type="hidden" />
                <div id="listing_suggestions" class="list-group mt-1"></div>
            </div>
            <div class="mb-3">
                <label>Type</label>
                <input name="type" class="form-control" value="featured" />
            </div>
            <div class="mb-3">
                <label>Priority</label>
                <input name="priority" type="number" class="form-control" value="0" />
            </div>
            <div class="mb-3">
                <label>Starts At</label>
                <input name="starts_at" type="datetime-local" class="form-control" />
            </div>
            <div class="mb-3">
                <label>Ends At</label>
                <input name="ends_at" type="datetime-local" class="form-control" />
            </div>
            <button class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    let search = document.getElementById('listing_search');
    let hidden = document.getElementById('listing_id');
    let sug = document.getElementById('listing_suggestions');

    let timeout = null;
    search.addEventListener('input', function(){
        clearTimeout(timeout);
        timeout = setTimeout(async function(){
            const q = search.value.trim();
            if (!q) { sug.innerHTML = ''; return; }
            const res = await fetch("/admin/listings/search?q="+encodeURIComponent(q), { headers: { 'X-Requested-With':'XMLHttpRequest' } });
            if (!res.ok) return;
            const json = await res.json();
            sug.innerHTML = '';
            (json.data || []).forEach(it => {
                const a = document.createElement('a');
                a.href = '#';
                a.className = 'list-group-item list-group-item-action';
                a.textContent = it.title + ' (#' + it.id + ')';
                a.dataset.id = it.id;
                a.addEventListener('click', function(e){ e.preventDefault(); hidden.value = this.dataset.id; search.value = this.textContent; sug.innerHTML = ''; });
                sug.appendChild(a);
            });
        }, 250);
    });
});
</script>
@endsection
