@props(['color'=>'danger'])
<div class="alert alert-{{$color}}" role="alert">
    {{$slot}}
</div>
