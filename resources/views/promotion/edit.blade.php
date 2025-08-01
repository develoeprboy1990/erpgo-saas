    {{Form::model($promotion,array('route' => array('promotion.update', $promotion->id), 'method' => 'PUT', 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">

    {{-- start for ai module--}}
    @php
        $plan= \App\Models\Utility::getChatGPTSettings();
    @endphp
    @if($plan->chatgpt == 1)
    <div class="text-end mb-3">
        <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['promotion']) }}"
           data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
            <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
        </a>
    </div>
    @endif
    {{-- end for ai module--}}
    <div class="row">
        <div class="form-group col-lg-6 col-md-6">
            {{ Form::label('employee_id', __('Employee'),['class'=>'form-label'])}}<x-required></x-required>
            {{ Form::select('employee_id', $employees,null, array('class' => 'form-control select','required'=>'required')) }}
            <div class="text-xs mt-1">
                {{ __('Create employee here.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Create employee') }}</b></a>
            </div>
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('designation_id',__('Designation'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::select('designation_id',$designations,null,array('class'=>'form-control select','required'=>'required'))}}
            <div class="text-xs mt-1">
                {{ __('Create designation here.') }} <a href="{{ route('designation.index') }}"><b>{{ __('Create designation') }}</b></a>
            </div>
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('promotion_title',__('Promotion Title'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('promotion_title',null,array('class'=>'form-control','required'=>'required', 'placeholder'=>__('Enter Promotion Title')))}}
        </div>
        <div class="form-group col-lg-6 col-md-6">
            {{Form::label('promotion_date',__('Promotion Date'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::date('promotion_date',null,array('class'=>'form-control','required'=>'required'))}}
        </div>
        <div class="form-group col-lg-12">
            {{Form::label('description',__('Description'),['class'=>'form-label'])}}
            {{Form::textarea('description',null,array('class'=>'form-control','placeholder'=>__('Enter Description')))}}
        </div>

    </div>
    </div>
    <div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>

    {{Form::close()}}
