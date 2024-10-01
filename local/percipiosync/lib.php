<?php

/**
 * This file is part of eAbyas
 *
 * Copyright eAbyas Info Solutons Pvt Ltd, India
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author eabyas  <info@eabyas.in>
 * @package eabyas
 * @subpackage local_percipiosync
 */
use local_percipiosync\plugin;

function percipiosyncstatus_filter($mform){
    $statusarray = array(plugin::SYNCERROR=>'Error',plugin::SYNCSUCCESS=>'Success',plugin::SYNCFAILED=>'Failed');
    $select = $mform->addElement('autocomplete', 'percipiosyncstatus', '', $statusarray, array('placeholder' => get_string('status')));
    $mform->setType('percipiosyncstatus', PARAM_RAW);
    $select->setMultiple(true);
}
function percipiosyncdatetime_filter($mform){
    $select = $mform->addElement('date_selector', 'percipiosyncdatetime',get_string('percipiosyncdatetime','local_percipiosync'), array('optional' => true));
    $mform->setType('percipiosyncdatetime', PARAM_INT);
}