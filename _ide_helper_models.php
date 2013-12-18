<?php
/**
 * An helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace {
/**
 * An Eloquent Model: 'Account'
 *
 * @property integer $id
 * @property integer $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property float $openingbalance
 * @property string $openingbalancedate
 * @property float $currentbalance
 * @property boolean $hidden
 * @property-read \User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transfer[] $transfersto
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transfer[] $transfersfrom
 * @property-read \Illuminate\Database\Eloquent\Collection|\Balancemodifier[] $balancemodifiers
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transaction[] $transactions
 * @method static Account notHidden() 
 */
	class Account {}
}

namespace {
/**
 * An Eloquent Model: 'Balancemodifier'
 *
 * @property integer        $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer        $account_id
 * @property string         $date
 * @property float          $balance
 * @property string         $balance_encrypted
 * @property-read \Account  $account
 * @method static Balancemodifier onDay($date) 
 * @method static Balancemodifier beforeDay($date) 
 */
	class Balancemodifier {}
}

namespace {
/**
 * An Eloquent Model: 'Component'
 *
 * @property integer                                                      $id
 * @property \Carbon\Carbon                                               $created_at
 * @property \Carbon\Carbon                                               $updated_at
 * @property \Carbon\Carbon                                               $deleted_at
 * @property integer                                                      $parent_component_id
 * @property string                                                       $type
 * @property string                                                       $name
 * @property integer                                                      $user_id
 * @property-read \Component                                              $parentComponent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[]   $childrenComponents
 * @property-read \Illuminate\Database\Eloquent\Collection|\Limit[]       $limits
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transaction[] $transactions
 * @property-read \User                                                   $user
 */
	class Component {}
}

namespace {
/**
 * An Eloquent Model: 'limit'
 *
 * @property integer         $id
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 * @property \Carbon\Carbon  $deleted_at
 * @property integer         $component_id
 * @property float           $amount
 * @property string          $date
 * @property-read \Component $component
 * @method static limit inMonth($date)
 */
	class Limit {}
}

namespace {
/**
 * An Eloquent Model: 'Transaction'
 *
 * @property integer                                                    $id
 * @property integer                                                    $user_id
 * @property integer                                                    $account_id
 * @property \Carbon\Carbon                                             $created_at
 * @property \Carbon\Carbon                                             $updated_at
 * @property string                                                     $description
 * @property float                                                      $amount
 * @property string                                                     $date
 * @property boolean                                                    $ignore
 * @property boolean                                                    $mark
 * @property integer                                                    $beneficiary_idX
 * @property integer                                                    $budget_idX
 * @property integer                                                    $category_idX
 * @property boolean                                                    $assigned
 * @property-read \Account                                              $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[] $components
 * @property-read \User                                                 $user
 * @method static Transaction inMonth($date)
 * @method static Transaction onDay($date)
 * @method static Transaction onDayOfMonth($date)
 * @method static Transaction betweenDates($start, $end)
 * @method static Transaction expenses()
 * @method static Transaction incomes()
 * @method static Transaction hasComponent($component) 
 */
	class Transaction {}
}

namespace {
/**
 * An Eloquent Model: 'Transfer'
 *
 * @property integer        $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer        $user_id
 * @property integer        $accountfrom_id
 * @property integer        $accountto_id
 * @property string         $description
 * @property float          $amount
 * @property string         $date
 * @property-read \Account  $accountfrom
 * @property-read \Account  $accountto
 * @property-read \User     $user
 * @method static Transfer inMonth($date)
 */
	class Transfer {}
}

namespace {
/**
 * An Eloquent Model: 'User'
 *
 * @property integer                                                      $id
 * @property \Carbon\Carbon                                               $created_at
 * @property \Carbon\Carbon                                               $updated_at
 * @property \Carbon\Carbon                                               $deleted_at
 * @property string                                                       $email
 * @property string                                                       $password
 * @property string                                                       $activation
 * @property string                                                       $reset
 * @property-read \Illuminate\Database\Eloquent\Collection|\Account[]     $accounts
 * @property-read \Illuminate\Database\Eloquent\Collection|\Component[]   $components
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transaction[] $transactions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Transfer[]    $transfers
 */
	class User {}
}

