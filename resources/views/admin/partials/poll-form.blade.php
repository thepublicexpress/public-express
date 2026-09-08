<div class="row g-3 mb-3">
    <div class="col-md-8"><label class="form-label">Poll title</label><input name="title" class="form-control" value="{{ old('title', $poll->title ?? '') }}" required></div>
    <div class="col-md-4"><label class="form-label">Start date</label><input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date', optional($poll?->start_date)->format('Y-m-d\\TH:i') ?? '') }}"></div>
    <div class="col-md-8"><label class="form-label">Description</label><textarea name="description" class="form-control">{{ old('description', $poll->description ?? '') }}</textarea></div>
    <div class="col-md-4"><label class="form-label">End date</label><input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date', optional($poll?->end_date)->format('Y-m-d\\TH:i') ?? '') }}"></div>
</div>
<label class="form-label">Questions और options</label>
@for($index = 0; $index < max(1, $poll?->questions->count() ?? 0); $index++)
    @php $question = $poll?->questions->sortBy('order_number')->values()->get($index); @endphp
    <div class="row g-2 mb-2">
        <div class="col-md-6"><input name="questions[{{ $index }}][question]" class="form-control" placeholder="सवाल" value="{{ $question->question ?? '' }}"></div>
        <div class="col-md-6"><textarea name="questions[{{ $index }}][options]" class="form-control" rows="1" placeholder="हर option नई line में लिखें">{{ collect($question?->options ?? [])->pluck('label')->implode("\n") }}</textarea></div>
    </div>
@endfor
<div class="form-text mb-3">हर सवाल के options अलग-अलग line में लिखें।</div>
