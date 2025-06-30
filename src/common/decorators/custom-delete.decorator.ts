import { applyDecorators, HttpCode, Delete } from '@nestjs/common';
import {
	ApiNoContentResponse,
	ApiNotFoundResponse,
	ApiOperation,
	ApiParam,
} from '@nestjs/swagger';

type DeleteDecoratorOptions = {
	path?: string;
	name: string;
	summary: string;
	description?: string;
	operationId: string;
};

export const CustomDelete = ({
	path,
	name,
	summary,
	description,
	operationId,
	// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
}: DeleteDecoratorOptions) =>
	applyDecorators(
		Delete(path ?? ':id'),
		HttpCode(204),
		ApiOperation({
			summary,
			description: description ?? `${summary}.`,
			operationId,
		}),
		ApiParam({
			name: 'id',
			description: `The ${name} ID.`,
			format: 'uuid',
		}),
		ApiNoContentResponse({
			description: `The ${name} has been successfully deleted.`,
		}),
		ApiNotFoundResponse({
			description: `${name} has not been found.`,
		}),
	);
