import { applyDecorators, Get } from '@nestjs/common';
import { ApiOkResponse, ApiOperation } from '@nestjs/swagger';

type GetManyDecoratorOptions = {
	path?: string;
	name: string;
	summary: string;
	description?: string;
	// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
	responseType: Function | [Function];
	operationId: string;
};

export const GetMany = ({
	path,
	name,
	summary,
	description,
	responseType,
	operationId,
	// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
}: GetManyDecoratorOptions) =>
	applyDecorators(
		Get(path ?? ''),
		ApiOperation({
			summary,
			description: description ?? `${summary}.`,
			operationId,
		}),
		ApiOkResponse({
			description: `${name} have been successfully retrieved.`,
			type: responseType,
		}),
	);
