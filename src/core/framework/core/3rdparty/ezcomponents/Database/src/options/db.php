<?php
/**
 * File containing the ezcDbSchemaOptions class
 *
 * @package Database
 * @version 1.0.0
 */
/**
 * Class containing the basic options for databases
 *
 * @property string $tableNamePrefix
 *                  Prefix which applyed to each table name
 * @version 1.0.0
 * @package Database
 */
class ezcDbOptions extends ezcBaseOptions
{
    /**
     * Constructor
     *
     * @param array $options Default option array
     * @return void
     * @ignore
     */
    public function __construct( array $options = array() )
    {
        $this->properties['tableNamePrefix'] = '';
        parent::__construct( $options );
    }

    /**
     * Set an option value
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     * @throws ezcBasePropertyNotFoundException
     *         If a property is not defined in this class
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @throws ezcBaseInvalidParentClassException
     *         if the class name passed as replacement for any of the built-in
     *         classes do not inherit from the built-in classes.
     * @return void
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'tableNamePrefix':
                if ( !is_string( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'string' );
                }
                $this->properties[$propertyName] = $propertyValue;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
                break;
        }
    }
}
