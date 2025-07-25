import { MissingOrIncorrectFieldsResponse } from '@/common/openapi/responses';
import { applyDecorators, Patch } from '@nestjs/common';
import {
	ApiBadRequestResponse,
	ApiBody,
	ApiConflictResponse,
	ApiNotFoundResponse,
	ApiOkResponse,
	ApiOperation,
	ApiParam,
} from '@nestjs/swagger';

type PatchDecoratorOptions = {
	path?: string;
	name: string;
	summary: string;
	description?: string;
	// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
	bodyType: Function;
	// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
	responseType: Function | [Function];
	operationId: string;
};

export const CustomPatch = ({
	path,
	name,
	summary,
	description,
	bodyType,
	responseType,
	operationId,
	// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
}: PatchDecoratorOptions) =>
	applyDecorators(
		Patch(path ?? ':id'),
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
		ApiBody({
			description: `The ${name}'s details.`,
			type: bodyType,
		}),
		ApiOkResponse({
			description: `The ${name} has been successfully updated.`,
			type: responseType,
		}),
		ApiNotFoundResponse({
			description: `${name} has not been found.`,
		}),
		ApiConflictResponse({
			description: `Another ${name} is in conflict with this one.`,
		}),
		ApiBadRequestResponse(MissingOrIncorrectFieldsResponse),
	);
