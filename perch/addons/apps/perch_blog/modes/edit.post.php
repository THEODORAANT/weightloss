<?php

    if (is_object($Post)) {
        $heading = $Lang->get('Editing Post ‘%s’', $HTML->encode($Post->postTitle()));
    }else{
        $heading = $Lang->get('Creating a New Post');
    }

    echo $HTML->title_panel([
            'heading' => $heading,
            ], $CurrentUser);


    include(__DIR__.'/_post_smartbar.php');

    if ($edit_mode=='define') {
        include('edit.define.post.php');
    }else{


        $template_help_html = $Template->find_help();
        if ($template_help_html) {
            echo $HTML->heading2('Help');
            echo '<div class="template-help">' . $template_help_html . '</div>';
        }

        if ($template =='post.html') {
            echo $HTML->heading2('Post');
        }else{
            echo '<h2 class="divider"><div>'.$HTML->encode(PerchUtil::filename($template, false)).'</div></h2>';
        }


        /* ---- FORM ---- */
        $lock_key = null;
        if (is_object($Post)) {
            $lock_key = 'blogpost:'.$Post->id();
        }
        echo $Form->form_start('blog-edit', '', $lock_key);


            /* ---- FIELDS FROM TEMPLATE ---- */
            $modified_details = $details;

            if (isset($modified_details['postDescRaw'])) {
                $modified_details['postDescHTML'] = $modified_details['postDescRaw'];
            }

            echo $Form->fields_from_template($Template, $modified_details);


            /* ---- TAGS ---- */
            #echo $Form->hint('Separate with commas');
            #echo $Form->text_field('postTags', 'Tags', isset($details['postTags'])?$details['postTags']:false);


            /* ---- COMMENTS ---- */
            #if ($CurrentUser->has_priv('perch_blog.comments.enable')) {
            #    echo $Form->checkbox_field('postAllowComments', 'Allow comments', '1', isset($details['postAllowComments'])?$details['postAllowComments']:'1');
            #}


            /* ---- POST TEMPLATES} ---- */
           #if (PerchUtil::count($post_templates)) {
           #    $opts = array();
           #    $opts[] = array('label'=>$Lang->get('Default'), 'value'=>'post.html');

           #    foreach($post_templates as $template) {
           #        $opts[] = array('label'=>PerchUtil::filename($template, false), 'value'=>'posts/'.$template);
           #    }
           #    echo $Form->hint('See sidebar note about post types');
           #    echo $Form->select_field('postTemplate', 'Post type', $opts, isset($details['postTemplate'])?$details['postTemplate']:'post.html');

           #}else{
               echo $Form->hidden('postTemplate', isset($details['postTemplate'])?$details['postTemplate']:$template);
           #}


            /* ---- AUTHORS ---- */
            #$authors = $Authors->all();
            #if (PerchUtil::count($authors)) {
            #    $opts = array();
            #    foreach($authors as $author) {
            #        $opts[] = array('label'=>$author->authorGivenName().' '.$author->authorFamilyName(), 'value'=>$author->id());
            #    }
            #    echo $Form->select_field('authorID', 'Author', $opts, isset($details['authorID'])?$details['authorID']:$Author->id());
            #}

            /* ---- SECTIONS ---- */
            #if (PerchUtil::count($sections)>1) {
            #    $opts = array();
            #    foreach($sections as $section) {
            #        $opts[] = array('label'=>$section->sectionTitle(), 'value'=>$section->id());
            #    }
            #    echo $Form->select_field('sectionID', 'Section', $opts, isset($details['sectionID'])?$details['sectionID']:1);
            #}


            /* ---- PUBLISHING ---- */


            if (is_object($Post)) {

                $opts = array();
                $opts[] = array('label'=>$Lang->get('Draft'), 'value'=>'Draft');
                if ($CurrentUser->has_priv('perch_blog.post.publish')) $opts[] = array('label'=>$Lang->get('Published'), 'value'=>'Published');
                echo $Form->select_field('postStatus', 'Status', $opts, isset($details['postStatus'])?$details['postStatus']:'Draft');

                // AI content generation button removed.
                echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());

            } else {

                echo $Form->hidden('authorID', $Author->id());
                echo $Form->hidden('postStatus', 'Draft');
                // AI content generation button removed.
                echo $Form->submit_field('btnSubmit', 'Create draft', $API->app_path());
            }


            

        echo $Form->form_end();
/*
<script>
(function () {
    var initialise = function () {
        var aiButton = document.getElementById('btnGenerateAI');
        if (!aiButton || !window.JSON || typeof JSON.stringify !== 'function' || typeof JSON.parse !== 'function') {
            return;
        }

        var requestAIContent = function (prompt, callback) {
            if (!prompt || typeof callback !== 'function') {
                return;
            }

            var url = '<?php echo PerchUtil::html(PERCH_LOGINPATH, true); ?>/addons/apps/perch_blog/ai/generate.php';
            var payload;

            try {
                payload = JSON.stringify({prompt: prompt});
            } catch (error) {
                callback(null);
                return;
            }

            var handleResponse = function (data) {
                try {
                    callback(data);
                } catch (error) {
                    // Swallow callback errors so the admin UI keeps working
                }
            };

            if (window.fetch) {
                fetch(url, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: payload
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(handleResponse)
                    .catch(function () {
                        handleResponse(null);
                    });
            } else if (window.XMLHttpRequest) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', url, true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                handleResponse(JSON.parse(xhr.responseText));
                            } catch (error) {
                                handleResponse(null);
                            }
                        } else {
                            handleResponse(null);
                        }
                    }
                };
                xhr.send(payload);
            }
        };

        aiButton.onclick = function (event) {
            event = event || window.event;
            if (event && typeof event.preventDefault === 'function') {
                event.preventDefault();
            } else if (event) {
                event.returnValue = false;
            }

            var prompt = window.prompt('Enter prompt for AI content');
            if (!prompt) {
                return false;
            }

            requestAIContent(prompt, function (data) {
                if (!data || !data.content) {
                    return;
                }

                var form = document.getElementById('blog-edit');
                var textarea = null;

                if (form) {
                    if (form.querySelector) {
                        textarea = form.querySelector('textarea');
                    } else {
                        var candidates = form.getElementsByTagName('textarea');
                        if (candidates && candidates.length) {
                            textarea = candidates[0];
                        }
                    }
                }

                if (!textarea && document.getElementsByTagName) {
                    var textareas = document.getElementsByTagName('textarea');
                    if (textareas && textareas.length) {
                        textarea = textareas[0];
                    }
                }

                if (textarea) {
                    textarea.value = data.content;
                }
            });

            return false;
        };
    };

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        initialise();
    } else if (document.addEventListener) {
        document.addEventListener('DOMContentLoaded', initialise);
    } else if (document.attachEvent) {
        document.attachEvent('onreadystatechange', function () {
            if (document.readyState === 'complete') {
                initialise();
            }
        });
    } else {
        var existingOnload = window.onload;
        window.onload = function () {
            if (typeof existingOnload === 'function') {
                try {
                    existingOnload();
                } catch (error) {
                    // Ignore errors from previous handlers
                }
            }
            initialise();
        };
    }
})();
</script>
*/
        /* ---- /FORM ---- */

    } // if edit_mode