<a href="{{ $detail_url }}" class="btn btn-xs btn-info" target="_blank">Detail</a>
@if($model->status == 'Requested')
| <a href="{{ $checked_url }}" class="btn btn-xs btn-primary">Checked</a>
| <a href="{{ $rejected_url }}" class="btn btn-xs btn-danger">Rejected</a>
@endif
