<?php
/*
 * @package     Parsing
 * @author      Frank Wikström <frank@mossadal.se>
 * @copyright   2015 Frank Wikström
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 *
 */

/**
 * @namespace MathParser::Parsing::Nodes
 *
 * Node classes for use in the generated abstract syntax trees (AST).
 */
namespace MathParser\Parsing\Nodes;

use MathParser\Lexing\Token;
use MathParser\Lexing\TokenType;
use MathParser\Interpreting\Evaluator;
use MathParser\Interpreting\ASCIIPrinter;
use MathParser\Interpreting\Visitors\Visitable;

/**
 * Abstract base class for nodes in the abstract syntax tree
 * generated by the Parser (and some AST transformers).
 *
 */
abstract class Node implements Visitable
{
    const NumericInteger = 1;
    const NumericRational = 2;
    const NumericFloat = 3;

    /**
     * Node factory, creating an appropriate Node from a Token.
     *
     * Based on the provided Token, returns a TerminalNode if the
     * token type is PosInt, Integer, RealNumber, Identifier or Constant
     * otherwise returns null.
     *
     * @return Node|null
     * @param Token $token Provided token
     */
    public static function rationalFactory(Token $token)
    {
        switch ($token->getType()) {
            case TokenType::PosInt:
            case TokenType::Integer:
                $x = intval($token->getValue());

                return new IntegerNode($x);
            case TokenType::RealNumber:
                $x = floatval(str_replace(',', '.', $token->getValue()));

                return new NumberNode($x);
            case TokenType::Identifier:
                return new VariableNode($token->getValue());
            case TokenType::Constant:
                return new ConstantNode($token->getValue());

            case TokenType::FunctionName:
                return new FunctionNode($token->getValue(), null);
            case TokenType::OpenParenthesis:
                return new SubExpressionNode($token->getValue());

            case TokenType::AdditionOperator:
            case TokenType::SubtractionOperator:
            case TokenType::MultiplicationOperator:
            case TokenType::DivisionOperator:
            case TokenType::ExponentiationOperator:
            case TokenType::GreaterThan:
            case TokenType::GreaterEqualThan:
                return new ExpressionNode(null, $token->getValue(), null);

            case TokenType::FactorialOperator:
                return new PostfixOperatorNode($token->getValue(), null);

            default:
                // echo "Node factory returning null on $token\n";
                return null;
        }
    }

    /**
     * Node factory, creating an appropriate Node from a Token.
     *
     * Based on the provided Token, returns a TerminalNode if the
     * token type is PosInt, Integer, RealNumber, Identifier or Constant
     * otherwise returns null.
     *
     * @return Node|null
     * @param Token $token Provided token
     */
    public static function factory(Token $token)
    {
        switch ($token->getType()) {
            case TokenType::PosInt:
            case TokenType::Integer:
                $x = intval($token->getValue());

                return new NumberNode($x);
            case TokenType::RealNumber:
                $x = floatval(str_replace(',', '.', $token->getValue()));

                return new NumberNode($x);
            case TokenType::Identifier:
                return new VariableNode($token->getValue());
            case TokenType::Constant:
                return new ConstantNode($token->getValue());

            case TokenType::FunctionName:
                return new FunctionNode($token->getValue(), null);
            case TokenType::OpenParenthesis:
                return new SubExpressionNode($token->getValue());

            case TokenType::AdditionOperator:
            case TokenType::SubtractionOperator:
            case TokenType::MultiplicationOperator:
            case TokenType::DivisionOperator:
            case TokenType::ExponentiationOperator:
            case TokenType::GreaterThan:
            case TokenType::GreaterEqualThan:
                return new ExpressionNode(null, $token->getValue(), null);

            case TokenType::FactorialOperator:
                return new PostfixOperatorNode($token->getValue());

            default:
                // echo "Node factory returning null on $token\n";
                return null;
        }
    }

    /**
     * Helper function, comparing two ASTs. Useful for testing
     * and also for some AST transformers.
     *
     * @return boolean
     * @param Node|null $other Compare to this tree
     */
    abstract public function compareTo($other);

    /**
     * Convenience function for evaluating a tree, using
     * the Evaluator class.
     *
     * Example usage:
     *
     * ~~~{.php}
     * $parser = new StdMathParser();
     * $node = $parser->parse('sin(x)cos(y)');
     * $functionValue = $node->evaluate( array( 'x' => 1.3, 'y' => 1.4 ) );
     * ~~~
     *
     * @return float
     * @param array $variables key-value array of variable values
     */
    public function evaluate($variables)
    {
        $evaluator = new Evaluator($variables);

        return $this->accept($evaluator);
    }

    /**
     * Rough estimate of the complexity of the AST.
     *
     * Gives a rough measure of the complexity of an AST. This can
     * be useful to choose between different simplification rules
     * or how to print a tree ("e^{...}" or ("\exp(...)") for example.
     *
     * More precisely, the complexity is computed as the sum of
     * the complexity of all nodes of the AST, and
     *
     *  NumberNodes, VariableNodes and ConstantNodes have complexity 1,
     *  FunctionNodes have complexity 5 (plus the complexity of its operand),
     *  ExpressionNodes have complexity 1 (for `+`, `-`, `*`), 2 (for `/`),
     *  or 8 (for `^`)
     *
     */
    public function complexity()
    {
        if ($this instanceof IntegerNode || $this instanceof VariableNode || $this instanceof ConstantNode) {
            return 1;
        } elseif ($this instanceof RationalNode || $this instanceof NumberNode) {
            return 2;
        } elseif ($this instanceof FunctionNode) {
            return 5 + $this->getOperand()->complexity();
        } elseif ($this instanceof ExpressionNode) {
            $operator = $this->getOperator();
            $left = $this->getLeft();
            $right = $this->getRight();
            switch ($operator) {
                case '+':
                case '-':
                case '*':
                    return 1 + $left->complexity() + (($right === null) ? 0 : $right->complexity());

                case '/':
                    return 3 + $left->complexity() + (($right === null) ? 0 : $right->complexity());

                case '^':
                    return 8 + $left->complexity() + (($right === null) ? 0 : $right->complexity());
            }
        }
        // This shouldn't happen under normal circumstances

        return 1000;
    }

    /**
     * Returns true if the node is a terminal node, i.e.
     * a NumerNode, VariableNode or ConstantNode.
     *
     * @return boolean
     *
     */
    public function isTerminal()
    {
        if ($this instanceof NumberNode) {
            return true;
        }

        if ($this instanceof IntegerNode) {
            return true;
        }

        if ($this instanceof RationalNode) {
            return true;
        }

        if ($this instanceof VariableNode) {
            return true;
        }

        if ($this instanceof ConstantNode) {
            return true;
        }

        return false;
    }

    public function getOperator()
    {
        return '';
    }

    public function __toString()
    {
        $printer = new ASCIIPrinter();

        return $this->accept($printer);
    }
}
