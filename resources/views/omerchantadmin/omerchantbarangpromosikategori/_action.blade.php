{!! Form::model($model,['url'=>$form_url,'method'=>'delete','class'=>'form-inline']) !!}
    <a href="{{ $edit_url}}" class="btn btn-xs btn-success">Edit</a>
    <a href="{{ $show_promosi}}" class="btn btn-xs btn-info" target="_blank">List Promo</a>
    {!! Form::submit('Delete', ['class'=>'btn btn-xs btn-danger js-submit-confirm','data-confirm-message'=>$confirm_message]) !!}
{!! Form::close() !!}