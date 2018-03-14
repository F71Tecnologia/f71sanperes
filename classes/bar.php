<?php
interface interface_class
{
    public function teach();
}

class PianoTeacher implements interface_class
{
    public function teach(Student $student)
    {
        $student->turnAbleTo('play piano');
    }
}

class GuitarTeacher implements interface_class
{
    public function teach(Student $student)
    {
        $student->turnAbleTo('play guitar');
    }
}

class Student
{
    private $abilities = array();

    public function study(interface_class $teacher);
    {
        $teacher->teach($this);
    }

    public function turnAbleTo($ability)
    {
        $this->abilities[] = $ability;
    }

    public function whatCanIDo()
    {
        $statement = "I'm able to:\n";
        foreach ($abilities as $ability) {
            $statement .= " - {$ability}\n";
        }
    }
}