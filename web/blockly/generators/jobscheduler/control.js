/**
 * Visual Blocks Language
 *
 * Copyright 2012 Google Inc.
 * http://blockly.googlecode.com/
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @fileoverview Generating JobScheduler for control blocks.
 * @author gasolin@gmail.com  (Fred Lin)
 */
'use strict';

goog.provide('Blockly.JobScheduler.loops');

goog.require('Blockly.JobScheduler');


Blockly.JobScheduler.controls_for = function() {
  // For loop.
  var variable0 = Blockly.JobScheduler.variableDB_.getName(
      this.getFieldValue('VAR'), Blockly.Variables.NAME_TYPE);
  var argument0 = Blockly.JobScheduler.valueToCode(this, 'FROM',
      Blockly.JobScheduler.ORDER_ASSIGNMENT) || '0';
  var argument1 = Blockly.JobScheduler.valueToCode(this, 'TO',
      Blockly.JobScheduler.ORDER_ASSIGNMENT) || '0';
  var branch = Blockly.JobScheduler.statementToCode(this, 'DO');
  if (Blockly.JobScheduler.INFINITE_LOOP_TRAP) {
    branch = Blockly.JobScheduler.INFINITE_LOOP_TRAP.replace(/%1/g,
        '\'' + this.id + '\'') + branch;
  }
  var code;
  if (argument0.match(/^-?\d+(\.\d+)?$/) &&
      argument1.match(/^-?\d+(\.\d+)?$/)) {
    // Both arguments are simple numbers.
    var up = parseFloat(argument0) <= parseFloat(argument1);
    code = 'for (' + variable0 + ' = ' + argument0 + '; ' +
        variable0 + (up ? ' <= ' : ' >= ') + argument1 + '; ' +
        variable0 + (up ? '++' : '--') + ') {\n' +
        branch + '}\n';
  } else {
    code = '';
    // Cache non-trivial values to variables to prevent repeated look-ups.
    var startVar = argument0;
    if (!argument0.match(/^\w+$/) && !argument0.match(/^-?\d+(\.\d+)?$/)) {
      var startVar = Blockly.JobScheduler.variableDB_.getDistinctName(
          variable0 + '_start', Blockly.Variables.NAME_TYPE);
      code += 'int ' + startVar + ' = ' + argument0 + ';\n';
    }
    var endVar = argument1;
    if (!argument1.match(/^\w+$/) && !argument1.match(/^-?\d+(\.\d+)?$/)) {
      var endVar = Blockly.JobScheduler.variableDB_.getDistinctName(
          variable0 + '_end', Blockly.Variables.NAME_TYPE);
      code += 'int ' + endVar + ' = ' + argument1 + ';\n';
    }
    code += 'for (' + variable0 + ' = ' + startVar + ';\n' +
        '    (' + startVar + ' <= ' + endVar + ') ? ' +
        variable0 + ' <= ' + endVar + ' : ' +
        variable0 + ' >= ' + endVar + ';\n' +
        '    ' + variable0 + ' += (' + startVar + ' <= ' + endVar +
            ') ? 1 : -1) {\n' +
        branch + '}\n';
  }
  return code;
};

Blockly.JobScheduler.controls_whileUntil = function() {
  // Do while/until loop.
  var until = this.getFieldValue('MODE') == 'UNTIL';
  var argument0 = Blockly.JobScheduler.valueToCode(this, 'BOOL',
      until ? Blockly.JobScheduler.ORDER_LOGICAL_NOT :
      Blockly.JobScheduler.ORDER_NONE) || 'false';
  var branch = Blockly.JobScheduler.statementToCode(this, 'DO');
  if (Blockly.JobScheduler.INFINITE_LOOP_TRAP) {
    branch = Blockly.JobScheduler.INFINITE_LOOP_TRAP.replace(/%1/g,
        '\'' + this.id + '\'') + branch;
  }
  if (until) {
    argument0 = '!' + argument0;
  }
  return 'while (' + argument0 + ') {\n' + branch + '}\n';
}
