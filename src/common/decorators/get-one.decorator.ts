import { applyDecorators, Get } from '@nestjs/common';
import {
	ApiNotFoundResponse,
	ApiOkResponse,
	ApiOperation,
	ApiParam,
} from '@nestjs/swagger';

type GetOneDecoratorOptions = {
	path?: string;
	name: string;
	summary: string;
	description?: string;
	// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
	responseType: Function | [Function];
	operationId: string;
};

export const GetOne = ({
	path,
	name,
	summary,
	description,
	responseType,
	operationId,
	// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
}: GetOneDecoratorOptions) =>
	applyDecorators(
		Get(path ?? ':id'),
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
		ApiOkResponse({
			description: `The ${name} has been successfully retrieved.`,
			type: responseType,
		}),
		ApiNotFoundResponse({
			description: `${name} has not been found.`,
		}),
	);
