@php
    /* @var \ReliqArts\StyleImporter\ConfigProvider $styleImporterConfigProvider */
    $skipImportVar = $styleImporterConfigProvider->getSkipStyleImportVariableName();
    $currentViewNameVar = $styleImporterConfigProvider->getCurrentViewNameVariableName();
@endphp

@if(!($$skipImportVar ?? false))
    {!! $styleImporter->import($stylesheetUrl, $$currentViewNameVar) !!}
@endif
