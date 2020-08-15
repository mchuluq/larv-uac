<?php

namespace Mchuluq\Laravel\Uac\Macros;

use Illuminate\Database\Query\Builder;

Builder::macro('filter',function($filter=null){
    if(!is_null($filter)){
        $this->where(function (Builder $query) use($filter) {
            $query->whereRaw($filter);
        });
    }
    return $this;
});