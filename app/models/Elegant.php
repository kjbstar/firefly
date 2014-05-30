<?php


class Elegant extends Eloquent
{


    protected $errors;
    protected $validator;
    protected $rules = [];
    protected $uniqueName = false;

    public function validate()
    {
        // make a new validator object

        $this->validator = Validator::make($this->toArray(), $this->rules);
        $this->errors = $this->validator->errors();
        // check for failure
        if ($this->validator->fails()) {
            return false;
        }
        if ($this->uniqueName === true && !is_null(Auth::user())) {
            $query = self::where('name', $this->name)->where('user_id', Auth::user()->id);
            if ($this->id) {
                $query->where('id', '!=', $this->id);
            }
            $count = $query->count();
            if ($count > 0) {
                $this->errors->add('name', 'This account name is not unique.');
                $this->validator->messages()->add('name', 'This account name is not unique.');
                return false;
            }
        }

        // validation pass
        return true;
    }

    /**
     * @return \Illuminate\Support\MessageBag
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * @return mixed
     */
    public function validator()
    {
        return $this->validator;
    }

} 