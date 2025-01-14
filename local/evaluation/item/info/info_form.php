<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or localify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once($CFG->dirroot.'/local/evaluation/item/evaluation_item_form_class.php');

class evaluation_info_form extends evaluation_item_form {
    protected $type = "info";

    public function definition() {

        $item = $this->_customdata['item'];
        $common = $this->_customdata['common'];
        $positionlist = $this->_customdata['positionlist'];
        $position = $this->_customdata['position'];
        $presentationoptions = $this->_customdata['presentationoptions'];

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'local_evaluation'));
        $mform->addElement('hidden', 'required', 0);
        $mform->setType('required', PARAM_INT);

        $mform->addElement('text',
                            'name',
                            get_string('item_name', 'local_evaluation'),
                            array('size'=>EVALUATION_ITEM_NAME_TEXTBOX_SIZE, 'maxlength'=>255));
        $mform->addElement('text',
                            'label',
                            get_string('item_label', 'local_evaluation'),
                            array('size'=>EVALUATION_ITEM_LABEL_TEXTBOX_SIZE, 'maxlength'=>255));

        $this->infotype = &$mform->addElement('select',
                                              'presentation',
                                              get_string('infotype', 'local_evaluation'),
                                              $presentationoptions);

        parent::definition();
        $this->set_data($item);

    }
}

