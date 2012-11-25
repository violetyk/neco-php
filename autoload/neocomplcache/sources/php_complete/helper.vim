let s:save_cpo = &cpo
set cpo&vim

if !exists('s:candidates_cache')
  let s:candidates_cache = {}
endif

function! neocomplcache#sources#php_complete#helper#get_internal_functions() "{{{
  if !has_key(s:candidates_cache, 'internal_functions')
    let s:candidates_cache.internal_functions = []
    let dict_file = s:get_dict_path('functions')
    if filereadable(dict_file)
      let candidates = []
      for line in readfile(dict_file)
        let cols        = split(line, '\t;\t')
        let func        = get(cols, 0, '')
        let title       = get(cols, 1, '')
        let description = get(cols, 2, '')
        let comment     = get(cols, 3, '')

        let info = ''
        let info = len(description) > 0 ? description : ''
        let info = len(comment) > 0 ? info."\n\n".comment : info

        call add(candidates, {
          \ 'word' : func,
          \ 'abbr' : func.'()',
          \ 'menu' : '[php] '.title,
          \ 'kind' : 'f',
          \ 'info' : info
          \})
      endfor
    endif
    let s:candidates_cache.internal_functions = candidates
  endif
  return s:candidates_cache.internal_functions
endfunction "}}}

function! s:get_dict_path(dict_name) "{{{
  let dict_files = split(globpath(&runtimepath, 'autoload/neocomplcache/sources/php_complete/'.a:dict_name.'.dict'), '\n')
  if empty(dict_files)
    return ''
  else
    return dict_files[0]
  endif
endfunction "}}}

let &cpo = s:save_cpo
unlet s:save_cpo
" vim:set fenc=utf-8 ff=unix ft=vim fdm=marker:
