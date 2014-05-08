[![Coverage Status](https://coveralls.io/repos/JC5/firefly/badge.png?branch=master)](https://coveralls.io/r/JC5/firefly?branch=master) [![Build Status](https://travis-ci.org/JC5/firefly.svg?branch=master)](https://travis-ci.org/JC5/firefly)

# Firefly
Firefly is a PHP based money manager. It's built to support my own finances, but
can be used by pretty much anybody.

The main philosophy of Firefly is this: at the _start_ of each month (regardless of when you get paid)
you have a set amount of money (say €1000) to spend during that month. Once the month is up, you start over.

Getting paid half-way through the month means you need to save that money somewhere until the month starts.

## Introduction
Firefly is pretty simple. First and foremost, Firefly can be used to create a
 bunch of accounts. Each account has an opening balance and a date for that
 balance. For example: your balance was EUR 100,- at the start of this year.

## Adding transactions
Once you have your accounts fixed you can start adding transactions. A
transaction is either money coming in or coming out. It has a description and
a date as well.

When you add transactions to each account, the balance will update (duh). If you do this
right, it should match the actual balance your bank says you have.

If you do this for a long enough time, you have a pretty useful financial
overview. Firefly can generate charts and reports which you can use to keep
an eye on your finances.

## Adding meta-data
Firefly supports an (if you feel like it) unlimited amount of meta-data fields, such as
beneficiaries (who did you pay), categories and budgets. These three are in fact
 added to firefly when you first install it.

Using these fields you can group and organize your transactions and quickly see where you're spending
money at.

## Control
Firefly generates various reports, and is capable of adding montly limits to your expenses. If you
create a budget called "Groceries", you can set up Firefly so it'll warn you when you spend more than (say)
150 euro's a month.

## Missing features and bugs
1. Firefly only supports the euro.
2. Firefly only supports monthly budgets.
3. Firefly was built to support _my_ finances. You might find it sucks.

## Would you like to know more?

Please read more about this on the [wiki pages](https://github.com/JC5/Firefly/wiki).