var amFeedFilter = Class.create({
    values_count: 0,
    url: $H({
        new_condition: null
    }),
    html: $H({
        conditions: {} 
   }),
    initialize: function(options, restore) {
        this.url.update(options.url);
        
        this.html.update(options.html);
        
        this.initPreloadEvents();
        
        if (restore != null){
            this.restoreValues(restore);
        } else {
            this.addNewValue();
        }
    },
    initPreloadEvents: function(){
        $('data-table').down("#new_value").observe("click", function(){
            this.addNewValue();
        }.bind(this))
    },
    initEvent: function($row, id, event, handler){
        var $el = $row.down("#" + id);
        
        if (!$el){
            $el = $row.down(id);
        }
        
        if ($el)
            $el.observe(event, handler);
        return $el;
    },
    initConditionEvents: function($row){
        this.initEvent($row, "delete_condition", "click", function(event, element){
            if (!element) element = event.element();
            this.deleteCondition($(element).up("#condition_row"));
        }.bind(this));
        
        this.initEvent($row, "change_condition", "change", function(event, element){
            if (!element) element = event.element();
            this.changeCondition($(element))
        }.bind(this));
        
        this.initEvent($row, "[rel=advanced_condition_empty]", "click", function(event, element){
            if (!element) element = event.element();
            this.changeValueEmpty($(element))
        }.bind(this));
    },
    initOutputEvents: function($row){
        
        this.initEvent($row, "delete_output", "click", function(event, element){
            if (!element) element = event.element();
            this.deleteOutput($(element));
        }.bind(this));
    },
    initEvents: function($row){
        
        this.initEvent($row, "new_condition", "change", function(event, element){
        
            if (!element) element = event.element();
        
            var type = element.options.selectedIndex ? 
                element.options[element.options.selectedIndex].getAttribute('data-type') :
                null;
            
            this.addCondition(element.value, type, $(element).up("#all_conditions_row"));
            element.value = "";
        }.bind(this));
        
        
        this.initEvent($row, "delete_value", "click", function(event, element){
            if (!element) element = event.element();
            if (confirm("Are you sure?")) {
                this.deleteValue($(element));
            }
        }.bind(this));
        
        this.initEvent($row, "add_output", "click", function(event, element){
            if (!element) element = event.element();
            this.addOutput($(element).up("#all_conditions_row"));
        }.bind(this));
        
    },
    deleteValue: function($element){
        var $all_conditions_row = $element.up('#all_conditions_row');
        
        if ($all_conditions_row.previousSibling &&
            $all_conditions_row.previousSibling.id == 'value_tpl_row'){
            $all_conditions_row.previousSibling.remove();
        }
        
        $all_conditions_row.remove();
        
        
    },
    deleteOutput: function($element){
         var $output_row = $element.up('#output_row');
         $output_row.remove();
    },
    addOutput: function($row){
        var $output_static_row = $row.down('#output_static_row');

        var $ret = this.getObjectFromHtml(this.getHtml("output_value", $row));
        
        $output_static_row.insert({
            'before': $ret
        });
        
        this.initOutputEvents($ret);
        
        return $ret;
    },
    changeValueEmpty: function($element){
        var uid = $element.getAttribute('uid');
        var $condition_row = $element.up("#condition_row");
        var $condition_value = $condition_row.down("#condition_value"); 
        var $empty_hidden = $condition_row.down("#advanced_" + uid + "_condition_empty_hidden");
        var $condition_operator = $condition_row.down('#condition_operator')
        
        if ($element.checked){
            $condition_value.setAttribute("readonly", true)
            $condition_value.addClassName("condition-value-disabled");
            $empty_hidden.value = 1;
            $condition_operator.select("[value!=eq][value!=neq]").each(function(el){
                el.disabled = true;
            })
        }
        else{
            $condition_value.removeClassName("condition-value-disabled")
            $condition_value.removeAttribute("readonly");
            $empty_hidden.value = 0;
            $condition_operator.select("[value!=eq][value!=neq]").each(function(el){
                el.disabled = false;
            })
        } 
            
    },
    changeCondition: function($element){
        var $condition_row = $element.up("#condition_row");
        
        this.deleteConditionTplRow($condition_row);
        
        this.addCondition(
            $element.value, 
            $element.options[$element.options.selectedIndex].getAttribute('data-type'),
            $element.up("#all_conditions_row"), 
            $condition_row,
            function(){
                this.deleteCondition($condition_row)
            }.bind(this)
        );
    },
    deleteConditionTplRow: function($row){
        if ($row.nextSibling &&
            $row.nextSibling.id == 'condition_tpl_row'){
            $row.nextSibling.remove();
        }
    },
    deleteCondition: function($row){
        this.deleteConditionTplRow($row);
        
        $row.remove();
    },
    addNewValue: function(noDefaultData){
        this.values_count++;
        
        var $row = $('tpl_row').cloneNode(true);
        $row.setAttribute('order', this.values_count);
        
        $row.removeClassName("tpl-row");
        $row.id = "all_conditions_row";

        var $output_value = $row.down('#output_value');
        var $modification = $row.down('#modification');
        
        if ($output_value){
            
            if (!noDefaultData){
                $output_value.update(this.getHtml("output_value", $row));
            }
            
            $output_value.update(this.getHtml("output_value_static", $row) + this.getHtml("new_output", $row));
        }
        
        if ($modification){
            $modification.update(this.getHtml("modification", $row));
        }
        
        $row.down('#actions').update(this.getHtml("actions", $row));
        $row.down('#new_condition').update(this.getHtml("new_condition", $row));
        
        
        if (this.values_count != 1){
            var valueTr = $('value_tpl_row').cloneNode(true);
            valueTr.removeClassName("tpl-row");
            
            $('conditions_table_body').insert(valueTr);
        }
        
        $('conditions_table_body').insert($row);
        
        this.initEvents($row);
        
        return $row;
    },
    restoreValues: function(conditions){
        for(var orderValue in conditions){
            var value = conditions[orderValue];
            
            if (typeof(value) !== 'function'){
                var $valueRow = this.addNewValue(true);
                this.restoreConditions(value.condition, $valueRow);
                this.restoreOutput(value.output, $valueRow);
                this.restoreModifications(value.modification, $valueRow);
            }
            

        }
    },
    restoreModifications: function(modification, $valueRow){
        if (modification && modification.value){
            $valueRow.down("#modificaiton_value").setValue(modification.value);
        }
    },
    restoreOutput: function(output, $valueRow){
        
        if (output && output.attribute){
            for(var orderAttribute in output.attribute){
                var attributeCode = output.attribute[orderAttribute];
                if (typeof(attributeCode) !== 'function'){
                    var parent = output.parent && output.parent[orderAttribute] ?
                        output.parent[orderAttribute] :
                        'off';

                    var $outputRow = this.addOutput($valueRow);

                    $outputRow.down("#output_attribute").setValue(attributeCode);
                    
                    if (parent == "on")
                        $outputRow.down("#output_operator").setValue(parent);
                }
            }

            
        }
        
        if (output && output['static'])
            $valueRow.down("#output_static").setValue(output['static']);
        
        
    },
    restoreConditions: function(condition, $valueRow){
        if (condition && condition.type)
            for(var orderAttribute in condition.type){
                var code = condition.attribute[orderAttribute];
                var type = condition.type[orderAttribute];
                
                if (typeof(type) !== 'function'){
                    var value = null;
                    var empty = null;
                    var operator = condition.operator[orderAttribute];
                    
                    if (condition.value && condition.value[orderAttribute])
                        value = condition.value[orderAttribute];
                    
                    if (condition.empty && condition.empty[orderAttribute])
                        empty = condition.empty[orderAttribute];

                    var $conditionRow = this.addCondition(code, type, $valueRow);

                    $conditionRow.down("#condition_operator").setValue(operator);
                    
                    if(value) {
                    $conditionRow.down("#condition_value").setValue(value);
                    }
                        
                    
                    if (empty && empty == 1){
                        var $ch = $conditionRow.down("[rel=advanced_condition_empty]");
                        $ch.setAttribute('checked', true);
                        this.changeValueEmpty($ch);
                        
                    }

                }
            }
    },
    c: 1,
    cuniq: function () {
        var d = new Date(),
            m = d.getMilliseconds() + "",
            u = ++d + m + (++this.c === 10000 ? (this.c = 1) : this.c);

        return u;
    },
    getHtml: function(code, $row){
        var ret = this.html.get(code);
        
        if (ret){
            ret = this.replace(ret, $row);
        }
            
        
        return ret;
    },
    replace: function(html, $row){
        html = html.replace(/:value_order/g, $row.getAttribute('order'));
        html = html.replace(/:uid/g, this.cuniq());
        return html;
    },
    getObjectFromHtml: function(data){
        var $tmpDiv = new Element("div");
        $tmpDiv.update(data);
            
        return $tmpDiv.firstChild;
    },
    addCondition: function(code, type, $row, $after, afterHanlder){
        
        var conditions = this.html.get("conditions");
        
        if (conditions[type] && conditions[type][code]){
//            var order = $row.getAttribute('order');
            
            var data = this.replace(conditions[type][code], $row);//conditions[type][code].replace(/:value_order/g, order);
            
            var $ret = this.getObjectFromHtml(data);
            
            if ($after){
                $after.insert({
                    'after': $ret
                });
            } else {
                $row.down('#condition').insert($ret)
            }
            
            
            var conditionTr = $('condition_tpl_row').cloneNode(true);
            conditionTr.removeClassName("tpl-row");

            $ret.insert({
                'after': conditionTr
            })
//            $ret.down('#condition').insert(conditionTr);
            

            if (typeof(afterHanlder) == 'function'){
                afterHanlder();
            }
            
            this.initConditionEvents($ret);
            return $ret;
            
        } else {
            new Ajax.Request(this.url.get("new_condition"), {
                parameters: {
                    code: code,
                    type: type
                },
                onSuccess: function(response) {
                    var json = response.transport.responseText.evalJSON();
                    if (!conditions[json.type]){
                        conditions[json.type] = {}
                    };
                    
                    conditions[json.type][json.code] = json.html;
                    this.addCondition(json.code, json.type, $row, $after, afterHanlder);
                    
                }.bind(this)
            });
        }
    }
})
